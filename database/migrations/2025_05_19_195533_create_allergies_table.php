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
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->foreign('patient_record_id')->references('id')->on('patient_records')->onDelete('set null');
            $table->enum('aller_power', ['strong', 'medium', 'weak'])->default('medium');
            $table->text('aller_name')->nullable();
            $table->enum('aller_type',['animal','pollen','Food','dust','mold','medicine','seasons','other'])->default('other');
            $table->text('aller_cause')->nullable();//المسبب
            $table->text('aller_treatment')->nullable();//العلاج
            $table->text('aller_pervention')->nullable();//المنوعات
            $table->text('aller_reasons')->nullable();//الأسباب

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergies');
    }
};
