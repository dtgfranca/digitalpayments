<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

}
