<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('credit_package_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->integer('credits');
            $table->string('status', 20)->default('pending');
            $table->string('payment_id', 255)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('invoice_number', 50)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};