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
        Schema::create('waiting_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->enum('w_status', ['waiting', 'in_progress', 'done'])->default('waiting');
            $table->timestamp('w_check_in_time')->nullable(); // وقت دخول المريض للانتظار
            $table->timestamp('w_start_time')->nullable(); // وقت بدء المعاينة
            $table->timestamp('w_end_time')->nullable(); // وقت انتهاء المعاينة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waiting_lists');
    }
};
