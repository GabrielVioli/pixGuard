<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cnpj_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj');
            $table->string('razao_social')->nullable();
            $table->string('situacao')->nullable();
            $table->string('data_abertura')->nullable();
            $table->string('cnae_descricao')->nullable();
            $table->json('socios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cnpj_analyses');
    }
};
