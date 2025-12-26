<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payer_id' => ['required'],
            'payee_id' => ['required'],
            'amount' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
