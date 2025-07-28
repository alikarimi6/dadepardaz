<?php

namespace App\Http\Requests\Iban;

use Illuminate\Foundation\Http\FormRequest;

class IbanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'iban' => 'required|size:2'
        ];
    }

    public function messages(): array {
        return [
            'iban.required' => 'iban is required',
            'iban.size' => 'iban is invalid',];
    }
}
