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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->string('specialist')->nullable();
            $table->string('cer_place')->nullable();//مكان الحصول على الشهادة
            $table->string('cer_name')->nullable();//اسم الشهادة
            $table->string('cer_images')->nullable();//صورة الشهادة
            $table->date('cer_date')->nullable();//تاريخ الحصول على الشهادة
            $table->string('exp_place')->nullable();//مكان الخبرة
            $table->string('exp_yesrs')->nullable();//سنوات الخبرة
            $table->string('biography')->nullable();//سيرة ذاتية
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->date('date_birth')->nullable();
            $table->string('age')->nullable();//بحيث يتم حساب العمر من تاريخ الميلاد و يتم االتحديث التقائي أي كل ما بيكبير سننة بيتحدث ل حالو
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
