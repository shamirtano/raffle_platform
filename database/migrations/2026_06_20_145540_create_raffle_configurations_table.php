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
        Schema::create('raffle_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Ej: 'raffle_type', 'raffle_quantity', 'raffle_price', 'raffle_start_date', 'raffle_end_date'
            $table->string('display_name'); // Nombre de la clave en Español
            $table->json('value'); // Almacena los valores en formato json
            $table->string('description')->nullable(); // Descripción de la clave en Español
            $table->string('type'); // Tipo de dato: 'string', 'integer', 'boolean', 'date', 'json'
            $table->boolean('is_active')->default(true); // Indica si la configuración está activa o no
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raffle_configurations');
    }
};
