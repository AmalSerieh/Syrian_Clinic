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
        Schema::create('diseases', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->foreign('patient_record_id')->references('id')->on('patient_records')->onDelete('set null');
            $table->foreignId('visit_id')->nullable()->constrained('visits')->cascadeOnDelete();
            $table->enum('d_type', ['current', 'chronic'])->default('current');
            $table->text('d_name')->nullable();
            $table->date('d_diagnosis_date')->nullable();
            $table->text('d_doctor')->nullable();
            $table->text('d_advice')->nullable();
            $table->text('d_prohibitions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diseases');
    }
};
