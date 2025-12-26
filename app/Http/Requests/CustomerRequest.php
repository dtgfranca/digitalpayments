<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:254'],
            'document' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'type' => ['required', 'string', 'in:REGULAR,MERCHANT'],
            'balance' => ['required', 'integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
