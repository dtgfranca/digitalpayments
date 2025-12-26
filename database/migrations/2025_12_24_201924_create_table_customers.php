<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->string('fullname');
            $table->string('document')->unique()->comment('CPF ou CNPJ');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('type', ['REGULAR', 'MERCHANT']);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
