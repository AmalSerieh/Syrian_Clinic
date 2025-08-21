<?php

namespace App\Repositories\Eloquent\Admin;

use App\Models\Room;
use App\Models\User;
use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Repositories\Admin\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function getLatestSecretary()
    {
        return User::where('role', 'secretary')->latest()->first();
    }
    public function secretaryExists(): bool
    {
        return User::where('role', 'secretary')->exists();
    }
    public function createSecretaryUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone' => $data['phone'],
            'role' => 'secretary',
            'created_by' => 'admin',
            'created_by_user_id' => auth()->id(),
        ]);
    }
    public function findSecretaryById(int $id)
    {
        return User::where('id', $id)->where('role', 'secretary')->firstOrFail();
    }
    public function updateSecretary($secretary, array $data)
    {
        return $secretary->update($data);
    }

    public function updateSecretaryProfile($secretary, array $data)
    {
        return $secretary->secretary()->update($data);
    }
    public function getAvailableRooms(string $language)
    {
        $rooms = Room::withCount('doctors')->get();

        // فلترة الغرف المتاحة
        $availableRooms = $rooms->filter(function ($room) {
            return $room->doctors_count < $room->room_capacity;
        })->map(function ($room) use ($language) {
            return [
                'id' => $room->id,
                'name' => $language === 'ar' ? $room->room_name_ar : $room->room_name_en,
                'specialty' => $language === 'ar' ? $room->room_specialty_ar : $room->room_specialty_en,
            ];
        });

        return $availableRooms;
    }
    public function createDoctor(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'role' => 'doctor',
            'created_by' => 'admin',
            'created_by_user_id' => auth()->id(),
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'photo' => 'doctor-profile-photos/default.jpg',
            'room_id' => $data['room_id'],
            'date_of_appointment' => $data['date_of_appointment'],
            'type_wage' => $data['type_wage'],
            'wage' => $data['wage']
        ]);
        // اجلب بيانات الغرفة بشكل صريح
        $room = Room::findOrFail($data['room_id']);

        DoctorProfile::create([
            'doctor_id' => $doctor->id,
            'specialist_ar' => $room->room_specialty_ar,
            'specialist_en' => $room->room_specialty_en,
            'gender' => $data['gender'],
        ]);


        return $doctor;
    }

    public function checkRoomCapacity(int $roomId): bool
    {
        $room = Room::withCount('doctors')->findOrFail($roomId);
        return $room->doctors_count < $room->room_capacity;
    }

    public function getRoomById(int $roomId)
    {
        return Room::findOrFail($roomId);
    }
    public function getAllDoctors()
    {
        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        
        return Doctor::with(['user', 'doctorProfile', 'room', 'doctorSchedule'])->get()->map(function ($doctor) use ($daysOfWeek) {

            // تجهيز جدول الأسبوع كامل
            $doctor->full_schedule = collect($daysOfWeek)->map(function ($day) use ($doctor) {
                $schedule = $doctor->doctorSchedule->firstWhere('day', $day);
                return [
                    'day' => ucfirst($day),
                    'start_time' => optional($schedule)->start_time,
                    'end_time' => optional($schedule)->end_time,
                    'has_shift' => !is_null($schedule),
                ];
            });

            // تحويل الجدول إلى JSON لتسهيل تمريره
            $doctor->full_schedule_json = $doctor->full_schedule->toJson();

            return $doctor;
        });
    }

}
