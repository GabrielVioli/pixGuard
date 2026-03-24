<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cpf_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('cpf', 11)->unique()->index();
            $table->string('nome');
            $table->string('situacao')->nullable();
            $table->char('genero', 1)->nullable();
            $table->date('nascimento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpf_analyses');
    }
};
