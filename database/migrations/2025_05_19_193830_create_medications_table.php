<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *///database\migrations\2025_05_19_193830_create_medications_table.php clinic_test
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_record_id')->nullable();
            $table->foreign('patient_record_id')->references('id')->on('patient_records')->onDelete('set null');
            $table->enum('med_type', ['current', 'chronic'])->default('current');
            $table->text('med_name')->nullable();
            $table->date('med_start_date')->nullable();
            $table->date('med_end_date')->nullable(); // null للدواء الدائم
            $table->enum('med_frequency', ['once_daily', 'twice_daily', 'three_times_daily', 'daily', 'weekly', 'monthly', 'yearly'])->default('once_daily');
            $table->decimal('med_frequency_value', 8, 2)->default(1); // رقم لحساب الكمية المستهلكة تلقائيًا
            $table->enum('med_dosage_form', ['tablet', 'capsule', 'syrup', 'liquid', 'powder', 'pills', 'drops', 'sprays', 'patches', 'injections'])->default('pills');
            $table->decimal('med_dose', 8, 2)->nullable();
            $table->enum('med_timing', ['before_food', 'after_food', 'morning','morning_evening','evening'])->nullable();
            $table->decimal('med_quantity_per_dose', 8, 2)->nullable(); // تُحسب تلقائياً
             $table->text('med_prescribed_by_doctor')->nullable();
            $table->decimal('med_total_quantity')->nullable();// إجمالي الكمية المحسوبة
            $table->decimal('med_taken_quantity')->nullable();// الكمية يلي صرت آخذ منو
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
/* public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $form = $this->input('med_dosage_form');
        $dose = $this->input('med_dose');

        if (in_array($form, ['tablet', 'capsule', 'pills'])) {
            $allowed = ['0.5', '1', '1.5', '2', '2.5'];
        } elseif (in_array($form, ['syrup', 'liquid', 'drops'])) {
            $allowed = ['5', '10', '15', '20', '25', '50', '100', '200'];
        } else {
            $allowed = []; // يمكن تركه مفتوح أو نرفض
        }

        if (!in_array((text)$dose, $allowed)) {
            $validator->errors()->add('med_dose', 'الجرعة المختارة غير مناسبة لنوع الدواء.');
        }
    });
}
 */
