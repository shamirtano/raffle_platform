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
        Schema::create('financiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['OWNER', 'INDIVIDUAL', 'COMPANY']);
            $table->string('email')->unique()->nullable();
            $table->decimal('participation_percentage', 5, 2)->default(0.00); 
            $table->decimal('capital_contributed', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financiers');
    }
};
