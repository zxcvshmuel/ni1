<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_effects', function (Blueprint $table) {
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('effect_id')->constrained()->cascadeOnDelete();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->primary(['invitation_id', 'effect_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_effects');
    }
};