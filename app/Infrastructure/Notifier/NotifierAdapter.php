<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Transfer\NotifyerInterface;
use App\Domain\Customer\Customer;
use League\Uri\Http;

class NotifierAdapter implements NotifyerInterface
{

    public function notify(Customer $payee): void
    {
        Http::post("https://util.devi.tools/api/v2/authorize");
    }
}
