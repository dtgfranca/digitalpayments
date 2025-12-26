<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Transfer\NotifyerInterface;
use App\Domain\ValueObjects\Amount;
use App\HttpClient;
use Illuminate\Support\Facades\Http;

class NotifierAdapter implements NotifyerInterface
{
    use HttpClient;

    public function notify(string $email, string $phone, int $amount): void
    {

        $response = Http::withHeaders([
            'User-Agent' => 'Laravel/12.0',
            'Accept' => 'application/json',
        ])->retry(3)->timeout(10)
            ->post('https://util.devi.tools/api/v1/notify', [
                'email' => $email,
                'phone' => $phone,
                'amount' => (new Amount($amount))->toFloat(),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Notification service unavailable');
        }
    }
}
