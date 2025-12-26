<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payee_id' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
