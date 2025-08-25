<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visit_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('treatment_stage')->default(0);
            $table->unsignedTinyInteger('treatment_final')->default(0);
            $table->unsignedTinyInteger('handling')->default(0);
            $table->unsignedTinyInteger('services')->default(0);

            $table->unsignedTinyInteger('final_evaluate')->default(0); // المعدل النهائي

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_evaluations');
    }
};
