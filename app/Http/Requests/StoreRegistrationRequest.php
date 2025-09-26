<?php
// app/Http/Requests/StoreRegistrationRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array {
        return [
            'name'          => ['required','string','max:255'],
            'callsign'      => ['nullable','string','max:50'],
            'city'          => ['nullable','string','max:120'],
            'email'         => ['nullable','email','max:255'],
            'phone'         => ['nullable','string','max:50'],

            // NOVO: categorias do titular
            'category_code' => ['required','in:V,R,E'],

            // Troca-Troca (inalterado)
            'trade_role'    => ['nullable','in:AMADOR,REVENDEDOR'],
            'trade_donation_pledge' => ['nullable','numeric','min:150'],

            'ticket_type'   => ['required','in:FULL,DAY'],
            'days'          => ['nullable','numeric','min:1','max:31'],

            // Acompanhantes
            'has_spouse'            => ['nullable','boolean'],
            'spouse_name'           => ['nullable','string','max:255'],
            'companions_count'      => ['nullable','integer','min:0','max:20'],
            'companions_names'      => ['array'],
            'companions_names.*'    => ['nullable','string','max:255'],

            // Crianças
            'children_count'        => ['nullable','integer','min:0','max:20'],
            'children_names'        => ['array'],
            'children_names.*'      => ['nullable','string','max:255'],

            // Produtos (checkboxes por SKU)
            'products'            => ['array'],
            'products.*.selected' => ['nullable','boolean'],
            'products.*.qty_full' => ['nullable','integer','min:0'],
            'products.*.qty_half' => ['nullable','integer','min:0'],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $d = $this->all();

            // Se DAY, obrigar 22/23
            if (($d['ticket_type'] ?? '') === 'DAY' &&
                !in_array(($d['days'] ?? ''), ['22','23'], true)) {
                $v->errors()->add('days', 'Selecione o dia que irá participar.');
            }

            // Se marcar cônjuge, exigir nome
            if (!empty($d['has_spouse']) && empty($d['spouse_name'])) {
                $v->errors()->add('spouse_name', 'Informe o nome do cônjuge.');
            }

            // Conferir quantidades x nomes de acompanhantes/crianças
            $cc = (int)($d['companions_count'] ?? 0);
            $cn = $d['companions_names'] ?? [];
            if ($cc > 0) {
                for ($i=0; $i<$cc; $i++) {
                    if (empty($cn[$i])) {
                        $v->errors()->add("companions_names.$i", "Informe o nome do acompanhante #".($i+1).".");
                    }
                }
            }

            $kc = (int)($d['children_count'] ?? 0);
            $kn = $d['children_names'] ?? [];
            if ($kc > 0) {
                for ($i=0; $i<$kc; $i++) {
                    if (empty($kn[$i])) {
                        $v->errors()->add("children_names.$i", "Informe o nome da criança #".($i+1).".");
                    }
                }
            }

            // Revendedor: doação mínima (R$)
            if (($d['trade_role'] ?? '') === 'REVENDEDOR') {
                $pledge = (float)($d['trade_donation_pledge'] ?? 0);
                if ($pledge < 150) {
                    $v->errors()->add('trade_donation_pledge', 'Revendedores devem comprometer doação mínima de R$ 150,00.');
                }
            }
        });
    }


    public function messages(): array {
        return [
          'category_code.in' => 'Categoria inválida.',
          'ticket_type.in'   => 'Tipo de inscrição inválido.',
        ];
    }


}
