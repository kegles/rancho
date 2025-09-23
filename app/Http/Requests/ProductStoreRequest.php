<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;


class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }


    public function rules(): array
    {
        return [
            'sku' => ['required','string','max:64','unique:products,sku'],
            'name'=> ['required','string','max:255'],
            'price_brl' => ['required','string'], // será convertido p/ centavos
            'is_child_half' => ['sometimes','boolean'],
            'active' => ['sometimes','boolean'],
        ];
    }


    protected function passedValidation(): void
    {
        // Converte price_brl → price (centavos) via mutator no Model
        $this->merge([
            'price' => null, // placeholder (usaremos price_brl no fill)
        ]);
    }
}
