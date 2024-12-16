<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('name');
            $table->json('payload');
            $table->boolean('locked')->default(false);
            $table->timestamps();

            $table->unique(['group', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};