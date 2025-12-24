<?php

namespace App\Domain\Transfer;

interface AuthorizerInterface
{
    public function authorize(): bool;
}
