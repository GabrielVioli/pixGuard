<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('pix_key_hash')->index();
            $table->string('type');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('proof_path')->nullable();
            $table->json('metadata')->nullable()->comment('Retorno bruto das APIs');
            $table->integer('risk_score')->default(0);
            $table->string('risk_level')->nullable();
            $table->json('details')->nullable()->comment('Lista de flags/motivos');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
