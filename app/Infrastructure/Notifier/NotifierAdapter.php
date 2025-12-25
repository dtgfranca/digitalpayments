<?php

namespace App\Infrastructure\Notifier;

use App\Domain\Transfer\NotifyerInterface;
use App\Domain\User\User;
use League\Uri\Http;

class NotifierAdapter implements NotifyerInterface
{

    public function notify(User $payee): void
    {
        Http::post("https://util.devi.tools/api/v2/authorize");
    }
}
