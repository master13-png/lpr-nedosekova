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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('code', 7)->unique();
            $table->string('name', 120);
            $table->string('form', 50);
            $table->integer('dosage_mg');
            $table->decimal('price', 10, 2);
            $table->boolean('by_prescription')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
