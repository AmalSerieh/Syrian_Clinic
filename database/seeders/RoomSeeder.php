<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rooms')->insert([
            ['room_name_ar' => 'غرفة رقم 1','room_name_en' => 'room number 1', 'room_specialty_ar' => 'عصبية', 'room_specialty_en' => 'Neurology', 'room_capacity' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['room_name_ar' => 'غرفة رقم 2','room_name_en' => 'room number 2', 'room_specialty_ar' => 'هضمية', 'room_specialty_en' => 'Digestive', 'room_capacity' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
