<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('email_address');
            $table->string('deliverability')->nullable();
            $table->decimal('quality_score', 4, 2)->nullable();
            $table->boolean('is_disposable')->default(false);
            $table->boolean('is_free_email')->default(false);
            $table->string('risk_status')->nullable();
            $table->integer('domain_age_days')->nullable();
            $table->integer('total_breaches')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_analyses');
    }
};
