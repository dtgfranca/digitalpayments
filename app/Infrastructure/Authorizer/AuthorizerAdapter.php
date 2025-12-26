<?php

namespace App\Infrastructure\Authorizer;

use App\Domain\Transfer\AuthorizerInterface;
use App\HttpClient;
use Illuminate\Support\Facades\Http;

class AuthorizerAdapter implements AuthorizerInterface
{
    use HttpClient;

    public function authorize(): bool
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel/12.0',
                'Accept' => 'application/json',
            ])->retry(3)->timeout(10)->get('https://util.devi.tools/api/v2/authorize');
            $body = $this->getContents($response);
            if($body['data']['authorization']) return true;
            return false;
        }catch (\Exception $e) {
            throw new \Exception('Error processing authorization');
        }
    }
}
