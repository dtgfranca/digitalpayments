<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'payer_id',
        'payee_id',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
