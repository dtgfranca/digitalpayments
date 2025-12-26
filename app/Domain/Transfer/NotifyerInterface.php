<?php

namespace App\Domain\Transfer;

interface NotifyerInterface
{
    public function notify(string $email, string $phone, int $amount): void;
}
