<?php

namespace App\Infrastructure\Authorizer;

use App\Domain\Transfer\AuthorizerInterface;
use Illuminate\Support\Facades\Http;

class AuthorizerAdapter implements AuthorizerInterface
{

    public function authorize(): bool
    {
       $response = Http::get('https://util.devi.tools/api/v2/authorize');
       if($response->getBody()->data->authorized) return true;
       return false;
    }
}
