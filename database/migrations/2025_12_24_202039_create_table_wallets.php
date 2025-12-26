<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->foreignId('customer_id')->unique()->constrained();
            $table->bigInteger('balance')->default(0)->comment('Armazenado em centavos');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
