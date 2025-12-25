<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'balance' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
