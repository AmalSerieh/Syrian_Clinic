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
        Schema::create('medication_alarms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->foreign('patient_record_id')->references('id')->on('patient_records')->onDelete('set null');
            $table->foreignId('medication_id')->constrained('medications')->onDelete('cascade');
            $table->time('alarm_time');         // الوقت
            $table->text('redundancy');       // التكرار: يوميًا، أسبوعيًا...
            $table->integer('quantity');        // الكمية: عدد الحبوب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_alarms');
    }
};
