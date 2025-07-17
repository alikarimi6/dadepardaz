<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreExpenseRequest extends FormRequest
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
            'category_id'   => ['required', 'exists:expense_categories,id'],
            'description'   => ['required', 'string', 'max:1000'],
            'amount'        => ['required', 'numeric', 'min:100'],
            'iban'         => ['required'],
            'national_code' => ['required', 'digits:10'],
            'attachment'    => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $iban = $this->input('iban');
            $national_code = $this->input('national_code');

            if ($iban && $national_code) {
                $exists = DB::table('users')
                ->join('user_ibans', 'users.id', '=', 'user_ibans.user_id')
                ->where('users.national_code', $national_code)
                ->where('user_ibans.code', $iban)
                ->exists();

                if (!$exists) {
                    $validator->errors()->add('iban', 'iban does not belong to you');
                }
            }
        });
    }
}
