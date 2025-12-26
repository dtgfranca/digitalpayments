<?php

namespace App\Domain\ValueObjects;

enum DocumentType:string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
}
