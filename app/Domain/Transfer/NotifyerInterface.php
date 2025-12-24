<?php

namespace App\Domain\Transfer;

use App\Domain\User\User;

interface NotifyerInterface
{
    public function notify(User $payee): void;
}
