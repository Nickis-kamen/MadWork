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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->enum('type', ['deposit', 'withdrawal', 'purchase', 'sale', 'subscription']);
            $table->enum('payment_stage', ['first','final'])->nullable();
            $table->enum('method', ['mvola', 'airtelmoney', 'orangemoney', 'visa'])->nullable();
            $table->string('number')->nullable();
            $table->float('amount');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();$table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plan')->nullable();
            $table->foreignId('devis_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->Text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
