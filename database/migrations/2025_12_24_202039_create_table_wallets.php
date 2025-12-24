<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->bigInteger('balance')->default(0); // Armazenado em centavos (Amount->value)
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
