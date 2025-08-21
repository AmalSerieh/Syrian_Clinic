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
        Schema::create('materials', function (Blueprint $table) {
             $table->id();
            $table->foreignId('secretary_id')->nullable()->constrained('secretaries')->cascadeOnDelete();
            $table->string('material_name')->nullable();
            $table->integer('material_quantity')->default(0);
            $table->string('material_location')->nullable();
            $table->date('material_expiration_date')->nullable();
            $table->decimal('material_price', 10, 2)->nullable();
            $table->integer('material_threshold')->nullable(); // حد النفاذ
             $table->integer('material_image')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
