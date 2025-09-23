<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }


    public function rules(): array
    {
        $id = $this->route('product');
        return [
            'sku' => ['required','string','max:64', Rule::unique('products','sku')->ignore($id)],
            'name'=> ['required','string','max:255'],
            'price_brl' => ['required','string'],
            'is_child_half' => ['sometimes','boolean'],
            'active' => ['sometimes','boolean'],
        ];
    }
}
