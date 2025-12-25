<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'id',
        'customer_id',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
