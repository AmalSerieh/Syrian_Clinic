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
        Schema::create('patient_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->boolean('profile_submitted')->default(false);
            $table->boolean('diseases_submitted')->default(false);
            $table->boolean('operations_submitted')->default(false);
            $table->boolean('medicalAttachments_submitted')->default(false);
            $table->boolean('allergies_submitted')->default(false);
            $table->boolean('family_history_submitted')->default(false);
            $table->boolean('medications_submitted')->default(false);
            $table->boolean('medicalfiles_submitted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_records');
    }
};
