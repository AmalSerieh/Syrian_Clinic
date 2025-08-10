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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->foreign('patient_record_id')->references('id')->on('patient_records')->onDelete('set null');
            $table->foreignId('visit_id')->nullable()->constrained('visits')->cascadeOnDelete();
            $table->text('op_name')->nullable();
            $table->text('op_doctor_name')->nullable();
            $table->text('op_hospital_name')->nullable();
            $table->date('op_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
