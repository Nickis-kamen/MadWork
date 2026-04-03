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
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('delai');
            $table->integer('delai_agency')->nullable();
            $table->text('description');
            $table->text('description_agency')->nullable();
            $table->string('cdc_file')->nullable();
            $table->float('budget');
            $table->float('price')->nullable();
            $table->enum('status', ['pending', 'proposed', 'accepted', 'refused'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
