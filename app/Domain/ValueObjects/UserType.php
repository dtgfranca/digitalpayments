<?php

namespace App\Domain\ValueObjects;

enum UserType: string
{
    case REGULAR = 'REGULAR';
    case MERCHANT = 'MERCHANT';

}
