<?php

namespace App\Http\Requests\Transition;

use Illuminate\Foundation\Http\FormRequest;

class TransitionUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'from_state_id' => ['sometimes|exists:states,id'],
            'to_state_id' => ['sometimes|exists:states,id]']
        ];
    }
}
