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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->foreignId('medication_id')->nullable()->constrained('medications')->onDelete('cascade'); // رابط مباشر لجدول الأدوية
            $table->enum('pre_type', ['current', 'chronic'])->default('current');
            $table->text('pre_name')->nullable();
            $table->text('pre_scientific')->nullable();
            $table->json('pre_alternatives')->nullable();
            $table->text('pre_trade')->nullable();
            $table->date('pre_start_date')->nullable();
            $table->date('pre_end_date')->nullable(); // null للدواء الدائم
            $table->enum('pre_frequency', ['once_daily', 'twice_daily', 'three_times_daily', 'daily', 'weekly', 'monthly', 'yearly'])->default('once_daily');
            $table->decimal('pre_frequency_value', 8, 2)->default(1); // رقم لحساب الكمية المستهلكة تلقائيًا
            $table->enum('pre_dosage_form', ['tablet', 'capsule', 'syrup', 'liquid', 'powder', 'pills', 'drops', 'sprays', 'patches', 'injections'])->default('pills');
            $table->decimal('pre_dose', 8, 2)->nullable();
            $table->enum('pre_timing', ['before_food', 'after_food', 'morning', 'morning_evening', 'evening'])->nullable();
            $table->decimal('pre_quantity_per_dose', 8, 2)->nullable(); // تُحسب تلقائياً
            $table->text('pre_prescribed_by_doctor')->nullable();
            $table->decimal('pre_total_quantity')->nullable();// إجمالي الكمية المحسوبة
            $table->decimal('pre_taken_quantity')->nullable();// الكمية يلي صرت آخذ منو
            $table->text('instructions')->nullable(); // ملاحظات إضافية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
