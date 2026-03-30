<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('region')->nullable();
            $table->string('line_type')->nullable();
            $table->boolean('is_voip')->default(false);
            $table->boolean('is_valid')->default(false);
            $table->string('line_status')->nullable();
            $table->string('risk_level')->nullable();
            $table->boolean('is_disposable')->default(false);
            $table->boolean('is_abuse_detected')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_analyses');
    }
};
