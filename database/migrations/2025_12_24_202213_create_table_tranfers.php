<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->foreignId('payer_id')->constrained('customers');
            $table->foreignId('payee_id')->constrained('customers');
            $table->bigInteger('amount'); // Valor em centavos
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tranfers');
    }
};
