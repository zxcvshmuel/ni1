<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_songs', function (Blueprint $table) {
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['invitation_id', 'song_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_songs');
    }
};