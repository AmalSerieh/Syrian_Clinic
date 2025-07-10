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
            $table->time('alarm_time'); // ساعة التنبيه مثل 10:00
            $table->date('alarm_start_date');
            $table->date('alarm_end_date')->nullable();
            $table->string('alarm_frequency'); // مثل daily, twice_daily...
            $table->float('alarm_frequency_value');
            $table->string('alarm_dosage_form'); // مثل tablet, syrup...
            $table->string('alarm_dose');
            $table->string('alarm_timing')->nullable(); // morning, after_food...
            $table->string('alarm_quantity_per_dose');
            $table->string('alarm_prescribed_by_doctor')->nullable();
            $table->float('alarm_total_quantity');

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
