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
        Schema::create('raffle_combos', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "Combo Bronce", "Paquete Familiar"
            $table->integer('tickets_count'); // Cantidad de números (Ej: 5, 10, 20)
            $table->decimal('discount_price', 12, 2)->nullable(); // Por si el combo ofrece un precio especial
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_combos');
    }
};
