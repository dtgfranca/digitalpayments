<?php

namespace App\Domain\Transfer;

use App\Domain\Customer\Customer;

interface NotifyerInterface
{
    public function notify(string $email, string $phone, int $amount): void;
}
