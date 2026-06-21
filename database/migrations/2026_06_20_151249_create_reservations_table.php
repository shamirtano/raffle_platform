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
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->enum('area', ['RESTAURANT', 'POOL', 'FAMILY_ZONE', 'EVENT_HALL']);
            $table->dateTime('reservation_time');
            $table->integer('guests_count');
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED'])->default('PENDING');
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
