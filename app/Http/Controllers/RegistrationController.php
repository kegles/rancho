<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\{Participant, Product, Registration, RegistrationItem, RegistrationAttendee};
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function createForm(Request $request)
    {
        $products = Product::orderBy('name')->get();
        return view('registration.new', compact('products'));
    }

    public function preview(StoreRegistrationRequest $req)
    {
        // Dados já validados (mas ainda não gravados)
        $data = $req->validated();

        // Normalizações
        $data['callsign'] = isset($data['callsign']) ? strtoupper($data['callsign']) : null;

        // Quantidades de pessoas
        $hasSpouse       = !empty($data['has_spouse']);
        $companionsCount = (int)($data['companions_count'] ?? 0);
        $childrenCount   = (int)($data['children_count'] ?? 0);
        $adultsCount     = 1 + ($hasSpouse ? 1 : 0) + $companionsCount;
        $totalPeople     = $adultsCount + $childrenCount;

        // Preço base (apenas do titular)
        $pricing = new PricingService();
        $isExempt = ($data['category_code'] === 'E');
        $base     = $pricing->basePrice($data['ticket_type'], $data['category_code'], $isExempt);

        // Produtos selecionados
        $selected = $data['products'] ?? [];
        $skus     = array_keys($selected);
        $catalog  = $skus ? Product::whereIn('sku', $skus)->get()->keyBy('sku') : collect();

        $items = [];
        $total = $base;

        foreach ($selected as $sku => $row) {
            if (empty($row['selected'])) continue;
            $p = $catalog->get($sku);
            if (!$p) continue;
            $price = (int)$p->price;

            if ($p->is_child_half) {
                if ($adultsCount > 0) {
                    $items[] = [
                        'sku' => $p->sku,
                        'name' => $p->name,
                        'unit_price' => $price,
                        'qty' => $adultsCount,
                        'subtotal' => $price * $adultsCount,
                        'audience' => 'adultos',
                    ];
                    $total += $price * $adultsCount;
                }
                if ($childrenCount > 0) {
                    $childUnit = intdiv($price, 2);
                    $items[] = [
                        'sku' => $p->sku,
                        'name' => $p->name . ' (crianças)',
                        'unit_price' => $childUnit,
                        'qty' => $childrenCount,
                        'subtotal' => $childUnit * $childrenCount,
                        'audience' => 'criancas',
                    ];
                    $total += $childUnit * $childrenCount;
                }
            } else {
                $items[] = [
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'unit_price' => $price,
                    'qty' => $totalPeople,
                    'subtotal' => $price * $totalPeople,
                    'audience' => 'todos',
                ];
                $total += $price * $totalPeople;
            }
        }

        // Montar attendees p/ mostrar no resumo
        $attendees = [];
        if ($hasSpouse) {
            $attendees[] = ['role'=>'SPOUSE','label'=>'Cônjuge','name'=>trim($data['spouse_name'] ?? '')];
        }
        foreach (($data['companions_names'] ?? []) as $i => $n) {
            if ($i < $companionsCount && $n) $attendees[] = ['role'=>'ACCOMP','label'=>'Acompanhante','name'=>trim($n)];
        }
        foreach (($data['children_names'] ?? []) as $i => $n) {
            if ($i < $childrenCount && $n) $attendees[] = ['role'=>'CHILD','label'=>'Criança','name'=>trim($n)];
        }

        // Salvar draft na sessão para confirmar depois
        $draft = [
            'data' => $data,
            'computed' => [
                'adultsCount' => $adultsCount,
                'childrenCount' => $childrenCount,
                'totalPeople' => $totalPeople,
                'base' => $base,
                'items' => $items,
                'total' => $total,
                'attendees' => $attendees,
            ],
        ];
        session(['reg.draft' => $draft]);

        // Mostrar resumo
        return view('registration.summary', [
            'draft' => $draft,
        ]);
    }



    public function confirm(Request $req)
    {
        $draft = session('reg.draft');
        abort_unless($draft, 400, 'Sessão expirada. Refaça a inscrição.');

        $d  = $draft['data'];
        $c  = $draft['computed'];

        return DB::transaction(function () use ($d, $c) {

            $pricing = new \App\Services\PricingService();
            $isExempt = ($d['category_code'] === 'E');
            $base     = $c['base'];
            $badge    = $pricing->badge($d['category_code']);

            // 1) Participant
            $registration = \App\Models\Registration::create([
                'participant_id' => $participant->id,
                'reg_number'     => str_pad((string)$participant->id, 3, '0', STR_PAD_LEFT),
                'badge_letter'   => $badge,
                'status'         => 'PENDING',
                'ticket_type'    => $d['ticket_type'],
                'days'           => $d['ticket_type']==='DAY'
                                    ? ($d['days'] ?? '')
                                    : '2025-11-22,2025-11-23',
                'base_price'     => $base,
                'total_price'    => 0,
                'eligible_draw'  => $pricing->eligibleForDraw($d['category_code'], $isExempt, $base),
            ]);

            // 2) Registration (reg_number com 3 dígitos)
            $registration = Registration::create([
                'participant_id' => $participant->id,
                'reg_number'     => str_pad((string)$participant->id, 3, '0', STR_PAD_LEFT),
                'status'         => 'PENDING',
                'ticket_type'    => $d['ticket_type'],
                'days'           => $d['days'] ?? '',
                'base_price'     => $c['base'],
                'total_price'    => 0, // atualiza no fim
                'eligible_draw'  => !$d['category_code']==='E' && $c['base']>0 && in_array($d['category_code'], ['V','R'], true),
            ]);

            // 3) Attendees extras
            foreach ($c['attendees'] as $a) {
                RegistrationAttendee::create([
                    'registration_id' => $registration->id,
                    'role' => $a['role'], // SPOUSE | ACCOMP | CHILD
                    'name' => $a['name'],
                ]);
            }

            // 4) Itens
            $products = Product::whereIn('sku', collect($c['items'])->pluck('sku')->unique())->get()->keyBy('sku');
            $sum = $c['base'];

            foreach ($c['items'] as $it) {
                $p = $products->get($it['sku']);
                if (!$p) continue;

                RegistrationItem::create([
                    'registration_id' => $registration->id,
                    'product_id'      => $p->id,
                    'qty'             => $it['qty'],
                    'unit_price'      => $it['unit_price'],
                    'subtotal'        => $it['subtotal'],
                ]);
                $sum += $it['subtotal'];
            }

            $registration->update(['total_price' => $sum]);

            // Limpa draft
            session()->forget('reg.draft');

            return redirect()
                ->route('registration.summary_final', ['id' => $registration->id])
                ->with('ok', 'Inscrição confirmada com sucesso!');
        });
    }

    // (Opcional) uma rota para mostrar o resumo final salvo, se quiser
    public function summaryFinal($id)
    {
        $reg = Registration::with(['participant','items.product','attendees'])->findOrFail($id);
        return view('registration.summary_final', compact('reg'));
    }
}
