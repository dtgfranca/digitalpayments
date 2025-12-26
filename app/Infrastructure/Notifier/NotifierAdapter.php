<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Customer\Customer;
use App\HttpClient;
use Illuminate\Support\Facades\Http;

class NotifierAdapter implements NotifyerInterface
{
    use HttpClient;
    public function notify(Customer $payee): void
    {

        try{
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel/12.0',
                'Accept' => 'application/json',
            ])->retry(3)->timeout(10)->post('https://util.devi.tools/api/v1/notify');


        }catch (\Exception $e) {
            throw new \Exception('Error processing notification');
        }


    }
}
