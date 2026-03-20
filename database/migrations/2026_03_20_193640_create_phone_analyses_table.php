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
        Schema::create('phone_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->index();
            $table->string('region')->nullable();
            $table->string('line_type')->nullable();
            $table->boolean('is_voip')->nullable();
            $table->boolean('is_valid');
            $table->string('line_status')->nullable();
            $table->string('risk_level')->nullable();
            $table->boolean('is_disposable')->nullable();
            $table->boolean('is_abuse_detected')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_analyses');
    }
};
