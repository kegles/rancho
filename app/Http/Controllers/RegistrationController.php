<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\{Participant, Product, Registration, RegistrationItem, RegistrationAttendee};
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class RegistrationController extends Controller
{
    public function createForm(Request $request)
    {
        $products = Product::orderBy('name')->get();
        return view('registration.new', compact('products'));
    }

    public function preview(StoreRegistrationRequest $req)
    {
        // 1) Dados validados
        $data = $req->validated();

        // 2) Normalizações
        $data['callsign'] = isset($data['callsign']) ? strtoupper($data['callsign']) : null;

        // 3) Quantidades de pessoas
        $hasSpouse       = !empty($data['has_spouse']);
        $companionsCount = (int)($data['companions_count'] ?? 0);
        $childrenCount   = (int)($data['children_count'] ?? 0);
        $adultsCount     = 1 + ($hasSpouse ? 1 : 0) + $companionsCount;
        $totalPeople     = $adultsCount + $childrenCount;

        // 4) Preço base
        $pricing  = new PricingService();
        $ticket   = $data['ticket_type']   ?? 'FULL';
        $cat      = $data['category_code'] ?? 'R';
        $isExempt = ($cat === 'E');

        $baseParticipant = $pricing->basePrice($ticket, $cat, $isExempt);

        // 5) Produtos selecionados (qty_full/qty_half)
        $selected = $data['products'] ?? [];   // [SKU => ['qty_full'=>..,'qty_half'=>..], ...]
        $skus     = array_keys($selected);
        $catalog  = $skus ? Product::whereIn('sku', $skus)->get()->keyBy('sku') : collect();

        $items = [];
        $total = 0; // começamos do zero e vamos somando

        // 5.1) Itens de base (sempre 1x participante; 1x cônjuge se houver)
        $items[] = [
            'sku'          => 'BASE',
            'name'         => 'Inscrição (Participante)',
            'unit_price'   => $baseParticipant,
            'qty_full'     => 1,
            'qty_half'     => 0,
            'subtotal'     => $baseParticipant,
            'accepts_half' => false,
        ];
        $total += $baseParticipant;

        if ($hasSpouse) {
            $items[] = [
                'sku'          => 'BASE_SPOUSE',
                'name'         => 'Inscrição (Cônjuge)',
                'unit_price'   => $baseParticipant,
                'qty_full'     => 1,
                'qty_half'     => 0,
                'subtotal'     => $baseParticipant,
                'accepts_half' => false,
            ];
            $total += $baseParticipant;
        }

        // 5.2) Demais produtos (com inteira/meia)
        foreach ($selected as $sku => $row) {
            $p = $catalog->get($sku);
            if (!$p) continue;

            $price   = (int) $p->price;                      // centavos
            $qtyFull = max(0, (int)($row['qty_full'] ?? 0));
            $qtyHalf = max(0, (int)($row['qty_half'] ?? 0));

            if (!$p->is_child_half) {
                $qtyHalf = 0; // força zero se produto não aceita meia
            }
            if (($qtyFull + $qtyHalf) === 0) continue;

            $halfUnit  = intdiv($price, 2);
            $lineTotal = ($qtyFull * $price) + ($qtyHalf * $halfUnit);
            $total    += $lineTotal;

            $items[] = [
                'sku'          => $p->sku,
                'name'         => $p->name,
                'unit_price'   => $price,
                'qty_full'     => $qtyFull,
                'qty_half'     => $qtyHalf,
                'subtotal'     => $lineTotal,
                'accepts_half' => (bool) $p->is_child_half,
            ];
        }

        // 6) Attendees (opcional, como já tinha)
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

        // 7) Doação de revendedor
        $donationCents = $this->moneyToCents($data['trade_donation_pledge'] ?? 0);
        if ($donationCents > 0) {
            $items[] = [
                'sku'          => 'DONATION',
                'name'         => 'Doação Revendedor',
                'unit_price'   => $donationCents,
                'qty_full'     => 1,
                'qty_half'     => 0,
                'subtotal'     => $donationCents,
                'accepts_half' => false,
            ];
            $total += $donationCents;
        }

        // 8) Draft
        $draft = [
            'data' => $data,
            'computed' => [
                'adultsCount'   => $adultsCount,
                'childrenCount' => $childrenCount,
                'totalPeople'   => $totalPeople,
                'base'          => $baseParticipant, // mantém se você usa em outro lugar
                'items'         => $items,
                'total'         => $total,
                'attendees'     => $attendees,
            ],
        ];
        session(['reg.draft' => $draft]);

        // 9) Produtos para a view (reaproveita o catálogo)
        $products = $catalog->values();

        // 10) Compatibilidade com summary que usa $c['items']
        $c = $draft['computed'];

        return view('registration.summary', [
            'draft'    => $draft,
            'c'        => $c,         // <— mantém seu foreach($c['items'])
            'products' => $products,
        ]);
    }


    public function confirm(Request $req)
    {
        $draft = session('reg.draft');
        abort_unless($draft, 400, 'Sessão expirada. Refaça a inscrição.');

        $d = $draft['data'];
        $c = $draft['computed'];

        return DB::transaction(function () use ($d, $c) {
            $pricing  = new PricingService();
            $isExempt = ($d['category_code'] === 'E');
            $base     = $c['base'];
            $badge    = $pricing->badge($d['category_code']);

            // 1) Participant (titular)
            $participant = Participant::create([
                'name'                  => trim($d['name']),
                'callsign'              => isset($d['callsign']) ? strtoupper($d['callsign']) : null,
                'city'                  => $d['city'] ?? null,
                'email'                 => $d['email'] ?? null,
                'phone'                 => $d['phone'] ?? null,
                'category_code'         => $d['category_code'], // V / R / E
                'trade_role'            => $d['trade_role'] ?? null,
                'trade_donation_pledge' => $d['trade_donation_pledge'] ?? null, // reais
            ]);

            // 2) Registration (uma única criação, com badge_letter)
            $registration = Registration::create([
                'participant_id' => $participant->id,
                'reg_number'     => str_pad((string)$participant->id, 3, '0', STR_PAD_LEFT),
                'badge_letter'   => $badge,
                'status'         => 'PENDING',
                'ticket_type'    => $d['ticket_type'],
                'days'           => $d['ticket_type'] === 'DAY'
                                    ? ($d['days'] ?? '')   // "22" ou "23"
                                    : '22,23',            //TODOS DIAS
                'base_price'     => $base,
                'total_price'    => 0,
                'eligible_draw'  => $pricing->eligibleForDraw($d['category_code'], $isExempt, $base),
            ]);

            // 3) Attendees extras
            foreach ($c['attendees'] as $a) {
                RegistrationAttendee::create([
                    'registration_id' => $registration->id,
                    'role'            => $a['role'], // SPOUSE | ACCOMP | CHILD
                    'name'            => $a['name'],
                ]);
            }

            // 4) Itens: gravar tudo que está no summary (base, cônjuge, produtos, doação)
            $sum = 0;

            // SKUs que vieram do summary
            $skus = collect($c['items'])->pluck('sku')->filter()->unique()->all();

            // 4.1) Garanta produtos técnicos para SKUs especiais
            $techNames = [
                'BASE'        => 'Inscrição (Participante)',
                'BASE_SPOUSE' => 'Inscrição (Cônjuge)',
                'DONATION'    => 'Doação Revendedor',
            ];

            foreach ($skus as $sku) {
                if (isset($techNames[$sku])) {
                    \App\Models\Product::firstOrCreate(
                        ['sku' => $sku],
                        [
                            'name'          => $techNames[$sku],
                            'price'         => 0,       // o valor efetivo vem de unit_price no item
                            'is_child_half' => false,
                        ]
                    );
                }
            }

            // 4.2) Catálogo (agora inclui os técnicos recém-criados)
            $catalog = \App\Models\Product::whereIn('sku', $skus)->get()->keyBy('sku');

            // 4.3) Inserir itens exatamente como no summary
            foreach ($c['items'] as $it) {
                $sku       = $it['sku'] ?? null;
                if (!$sku) continue;

                $p         = $catalog->get($sku);
                if (!$p) continue; // segurança; após firstOrCreate, deve existir

                $unitPrice = (int) ($it['unit_price'] ?? 0);          // centavos
                $qtyFull   = max(0, (int) ($it['qty_full'] ?? ($it['qty'] ?? 0)));
                $qtyHalf   = max(0, (int) ($it['qty_half'] ?? 0));

                // se produto não aceita meia, força zero
                if (!$p->is_child_half) {
                    $qtyHalf = 0;
                }

                // calcula subtotal (meia = 50%)
                $halfUnit  = intdiv($unitPrice, 2);
                $lineTotal = ($qtyFull * $unitPrice) + ($qtyHalf * $halfUnit);
                $sum      += $lineTotal;

                \App\Models\RegistrationItem::create([
                    'registration_id' => $registration->id,
                    'product_id'      => $p->id,
                    'sku'             => $p->sku,
                    'qty_full'        => $qtyFull,
                    'qty_half'        => $qtyHalf,
                    'unit_price'      => $unitPrice,
                    // Se sua tabela tiver a coluna 'subtotal', pode gravar também:
                    // 'subtotal'     => $lineTotal,
                    // Se tiver a coluna 'name' no item e quiser guardar o rótulo exibido:
                    // 'name'         => $it['name'] ?? null,
                ]);
            }

            // total da inscrição = soma dos itens do summary (não some "base" de novo aqui)
            $registration->update(['total_price' => $sum]);

            // Limpa draft da sessão
            session()->forget('reg.draft');

            return redirect()
                ->route('registration.pay', $registration->id)
                ->with('ok', 'Inscrição registrada! Faça o pagamento usando o PIX abaixo.');
        });
    }

    // (Opcional) Resumo final persistido
    public function summaryFinal($id)
    {
        $reg = Registration::with(['participant','items.product','attendees'])->findOrFail($id);
        return view('registration.summary_final', compact('reg'));
    }

    // Monta um campo EMV (ID + length + value)
    private function emv(string $id, string $value): string
    {
        $len = strlen($value);
        return $id . str_pad((string)$len, 2, '0', STR_PAD_LEFT) . $value;
    }

    // Calcula CRC16-CCITT (0x1021) para BR Code
    private function crc16(string $payload): string
    {
        $polynomial = 0x1021;
        $result = 0xFFFF;

        for ($offset = 0; $offset < strlen($payload); $offset++) {
            $result ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($result & 0x8000) !== 0) {
                    $result = (($result << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $result = ($result << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
    }

    // Gera o BR Code PIX (EMV) com chave, valor e TXID
    private function makePixBrCode(string $pixKey, int $amountCents, string $txid, string $merchantName = 'RANCHO', string $merchantCity = 'PELOTAS'): string
    {
        // Campos fixos/obrigatórios
        $payloadFormat   = $this->emv('00', '01');         // Payload Format Indicator
        $pointOfMethod   = $this->emv('01', '12');         // P.OI Method (12 = dinâmico; 11 = estático) -> use 12 se gerar QR dinâmico com consulta; para copia-e-cola estático use 11
        // Com BCB GUI (chave)
        $gui             = $this->emv('00', 'br.gov.bcb.pix');
        $guiKey          = $this->emv('01', $pixKey);
        // (Opcional) descrição curta: ID 02
        // $desc        = $this->emv('02', 'Inscricao Rancho');

        $merchantAccount = $this->emv('26', $gui . $guiKey /* . $desc */);

        $merchantCat     = $this->emv('52', '0000');       // MCC
        $currency        = $this->emv('53', '986');        // BRL
        $amount          = $this->emv('54', number_format($amountCents / 100, 2, '.', '')); // 2 casas, ponto
        $country         = $this->emv('58', 'BR');
        $name            = $this->emv('59', mb_strimwidth($merchantName, 0, 25, '', 'UTF-8'));
        $city            = $this->emv('60', mb_strimwidth($merchantCity, 0, 15, '', 'UTF-8'));

        // Additional Data Field Template (TXID)
        $txidField       = $this->emv('05', substr($txid, 0, 25));
        $addData         = $this->emv('62', $txidField);

        // Monta sem o CRC (ID 63)
        $base = $payloadFormat
            . $pointOfMethod
            . $merchantAccount
            . $merchantCat
            . $currency
            . $amount
            . $country
            . $name
            . $city
            . $addData;

        // CRC = ID '63' + len '04' + checksum calculado sobre (base + '6304')
        $toCrc = $base . '6304';
        $crc   = $this->crc16($toCrc);

        return $base . '63' . '04' . $crc;
    }

    // --------------- Novas Actions ----------------

    // Página de pagamento com QR + código PIX
    public function pay($id)
    {
        $reg = Registration::with('participant')->findOrFail($id);

        // Valor total e número de inscrição
        $amountCents = (int) $reg->total_price;
        $txid        = (string) $reg->reg_number;

        //Informações do ENV
        $pixKey = env('RANCHO_PIX_KEY');
        abort_if(empty($pixKey), 500, 'Chave PIX não configurada (RANCHO_PIX_KEY).');

        $merchantName = env('RANCHO_PIX_NAME');
        abort_if(empty($merchantName), 500, 'Chave PIX não configurada (RANCHO_PIX_NAME).');

        $merchantCity = env('RANCHO_MERCHANT_CITY');
        abort_if(empty($merchantName), 500, 'Chave PIX não configurada (RANCHO_MERCHANT_CITY).');

        // Gera o código "copia e cola" (EMV)
        $pixCode = $this->makePixBrCode($pixKey, $amountCents, $txid, $merchantName, $merchantCity);

        // URL que serve o QR (PNG)
        $qrUrl = route('registration.qr', $reg->id);

        return view('registration.pay', compact('reg', 'pixCode', 'qrUrl'));
    }

    // Imagem do QR a partir do BR Code
    public function qr($id)
    {
        $reg = Registration::findOrFail($id);

        $pixKey = env('RANCHO_PIX_KEY');
        abort_if(empty($pixKey), 500, 'Chave PIX não configurada (RANCHO_PIX_KEY).');
        $merchantName = env('RANCHO_MERCHANT_NAME', 'RANCHO RADIOAMADOR');
        abort_if(empty($merchantName), 500, 'Chave PIX não configurada (RANCHO_MERCHANT_NAME).');
        $merchantCity = env('RANCHO_MERCHANT_CITY', 'PELOTAS');
        abort_if(empty($merchantCity), 500, 'Chave PIX não configurada (RANCHO_MERCHANT_CITY).');

        // ⚠️ Ajuste: método estático "11" (QR estático) é o mais indicado p/ copia-e-cola
        $pixCode = $this->makePixBrCode(
            $pixKey,
            (int) $reg->total_price,
            (string) $reg->reg_number,
            $merchantName,
            $merchantCity
        );

        $svg = QrCode::format('svg')
            ->size(360)
            ->margin(2)
            ->generate($pixCode);

        return response($svg)->header('Content-Type', 'image/svg+xml');
    }


    // Converte "1.234,56" / "1234.56" / "1234" em CENTAVOS (int)
    private function moneyToCents($value): int
    {
        $s = preg_replace('/[^\d,.\-]/', '', (string)$value); // mantém dígitos, vírgula, ponto, sinal
        $s = str_replace([' ', '.'], '', $s);                 // remove milhar
        $s = str_replace(',', '.', $s);                       // vírgula → ponto
        $f = is_numeric($s) ? (float)$s : 0.0;
        return (int) round($f * 100);
    }


}
