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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('start_time');
            $table->date('end_time');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('flat_id')->constrained('flats')->onDelete('cascade');
            $table->decimal('price',8,2);
            $table->string('status')->default('pending'); // pending, approved, cancelled,rejected

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
