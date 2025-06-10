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
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_record_id')->nullable()->constrained('patient_records')->onDelete('set null');
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->date('date_birth')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->enum('blood_type', ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'])->default('O+');
            $table->boolean('smoker')->default(0);
            $table->boolean('alcohol')->default(0);
            $table->enum('matital_status', ['single', 'married', 'widower', 'divorced'])->default('single'); // نوع المستخدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};
