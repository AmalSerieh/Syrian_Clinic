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
        Schema::create('doctor_materials', function (Blueprint $table) {
           $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('visit_id')->nullable()->constrained('visits')->onDelete('set null');
            $table->integer('dm_quantity');
            $table->tinyInteger('dm_quality')->nullable(); // تقييم المورد
            $table->timestamp('dm_used_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_materials');
    }
};
