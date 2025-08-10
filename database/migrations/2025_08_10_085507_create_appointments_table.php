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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('secretary_id')->nullable();
            $table->date('date'); // اليوم الذي تم الحجز فيه
            $table->string('day'); // اليوم الذي تم الحجز فيه
            $table->time('start_time'); // وقت بداية الحجز
            $table->time('end_time');   // وقت نهاية الحجز 'Pending payment','Processing','Confirmed','Cancelled','Completed','On Hold','Rescheduled','No Show'
            $table->enum('status', ['pending', 'confirmed', 'completed', 'canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary', 'processing'])->default('pending');
            $table->enum('location_type', ['in_Home', 'on_Street', 'in_Clinic', 'at_Doctor','in_Payment', 'finished'])->default('in_Home');
            $table->time('arrivved_time');   // الوقت الذي يحتاجه للوصول للعيادة
            $table->enum('created_by', ['patient', 'secretary',])->default('patient');//من قبل مين تم حجز الموعد  إذا من التطبيق ف هو مرسض و إذا
            $table->enum('type_visit', ['appointment', 'review'])->default('appointment');//من قبل مين تم حجز الموعد  إذا من التطبيق ف هو مرسض و إذا
            $table->softDeletes();
            //$table->unsignedBigInteger('payment_id')->nullable();
            // $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->timestamps();

            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('secretary_id')->references('id')->on('secretaries')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
