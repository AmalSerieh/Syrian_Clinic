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
        Schema::create('visits', function (Blueprint $table) {
             $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('v_started_at')->nullable();
            $table->dateTime('v_ended_at')->nullable();
            $table->enum('v_status', ['active', 'in_payment','completed']);
            $table->decimal('v_price', 10, 2)->nullable();
            $table->boolean('v_paid')->default(false);
            $table->enum('v_payment_method', ['cash', 'card', 'transfer'])->default('cash');

            $table->text('v_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
