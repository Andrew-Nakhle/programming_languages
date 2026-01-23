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
        Schema::create('flats', function (Blueprint $table) {
            $table->id();
            $table->string('governorate');
            $table->string('city');
            $table->decimal('price',8,2);
            $table->date('available_date')->nullable();

            //ركز هون تابع constrained بشكل ديناميكي عرف user_id كا unsigned big integer
            //on delete: mean when the user deleted all the flats will be deleting
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('section');
            $table->string('status')->default('available'); // available, booked,sold
            $table->text('address');
            $table->string('flat_image')->nullable();
            $table->integer('rooms');
            $table->integer('space');
            $table->boolean('has_elevator')->default(false);
            $table->boolean('is_furnished')->default(false);
            $table->integer('floor')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flats');
    }
};
