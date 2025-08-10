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
        Schema::create('rooms', function (Blueprint $table) {
           $table->id();
            $table->string('room_name_ar'); // اسم الغرفة بالعربي
            $table->string('room_name_en'); // اسم الغرفة بالإنجليزي
            $table->string('room_specialty_ar'); // مثلاً: "عصبية"
            $table->string('room_specialty_en'); // مثلاً: "Neurology"
            $table->integer('room_capacity')->default(3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
