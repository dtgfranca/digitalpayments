<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'fullname',
        'email',
        'document',
        'password',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
