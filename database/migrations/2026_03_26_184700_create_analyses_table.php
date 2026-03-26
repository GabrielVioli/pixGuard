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
            $table->string("name")->nullable()->comment('Nome do destinatário informado');
            $table->string("pix_key")->index();
            $table->string('type')->comment('CPF, CNPJ, Email, etc');
            $table->decimal("amount", 15, 2)->default(0);
            $table->string("proof_path")->nullable()->comment('Caminho do screenshot no storage');
            $table->json("metadata")->nullable();
            $table->integer("risk_score")->default(0);
            $table->string("risk_level")->nullable()->comment('Seguro, Atenção, Alto Risco');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
