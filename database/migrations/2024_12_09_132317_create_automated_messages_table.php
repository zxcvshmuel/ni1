<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automated_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->json('name');
            $table->json('content');
            $table->json('settings');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automated_messages');
    }
};