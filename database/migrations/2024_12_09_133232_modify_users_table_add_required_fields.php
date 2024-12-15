<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Existing fields from Laravel's default migration:
            // id (bigint unsigned)
            // name (varchar(255))
            // email (varchar(255))
            // password (varchar(255))
            // remember_token (varchar(100))
            // timestamps()

            // Add new columns
            $table->integer('credits')->default(0)->after('password');
            $table->string('phone', 20)->nullable()->after('credits');
            $table->string('language', 5)->default('he')->after('phone');
            $table->boolean('is_active')->default(true)->after('language');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['credits', 'phone', 'language']);
            $table->dropSoftDeletes();
        });
    }
};