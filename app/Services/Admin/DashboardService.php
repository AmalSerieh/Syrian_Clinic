<?php

namespace App\Services\Admin;

use App\Models\Doctor;
use App\Models\Room;
use App\Models\Secretary;
use App\Repositories\Admin\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class DashboardService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DashboardRepositoryInterface $repo)
    {

    }
    public function getDashboardData()
    {
        // ممكن لاحقًا نضيف بيانات أخرى للداشبورد هنا
        $secretary = $this->repo->getLatestSecretary();
        $totalRooms = Room::count();
        $usedRooms = Doctor::whereNotNull('room_id')->count();


        $roomsFull = Room::all()->every(function ($room) {
            return $room->doctors()->count() >= $room->room_capacity;
        });

        $roomsFull1 = $usedRooms >= $totalRooms;

        return compact('secretary', 'roomsFull');
    }

    public function shouldRedirectToList(): bool
    {
        return $this->repo->secretaryExists();
    }
    public function storeSecretary(array $validated): void
    {
        DB::beginTransaction();

        try {
            // إنشاء المستخدم
            $user = $this->repo->createSecretaryUser($validated);

            // إنشاء السجل الخاص بالسكرتيرة
            Secretary::create([
                'user_id' => $user->id,
                'photo' => 'avatars/defaults.jpg',
                'gender' => $validated['gender'],
                'date_of_appointment' => $validated['date_of_appointment'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e; // سنعالج الاستثناء في Controller
        }
    }
    public function getSecretaryById(int $id)
    {
        return $this->repo->findSecretaryById($id);
    }
    public function updateSecretary(array $data)
    {
        DB::beginTransaction();
        try {
            $secretary = $this->repo->getLatestSecretary();
            if (!$secretary) {
                throw new \Exception('لا توجد سكرتيرة حالياً');
            }

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $this->repo->updateSecretary($secretary, $updateData);

            $this->repo->updateSecretaryProfile($secretary, [
                'date_of_appointment' => $data['date_of_appointment'],
                's_wage' => $data['s_wage'],
                's_type_wage' => $data['s_type_wage'],
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // نرمي الاستثناء للكنترولر ليعالج الرد
        }
    }
    public function getAvailableRooms()
    {
        $language = app()->getLocale(); // 'ar' أو 'en'
        return $this->repo->getAvailableRooms($language);
    }
    public function storeDoctor(array $data)
    {
        DB::beginTransaction();

        try {
            if (!$this->repo->checkRoomCapacity($data['room_id'])) {
                throw new \Exception('هذه الغرفة ممتلئة بالفعل بالأطباء.');
            }

            $room = $this->repo->getRoomById($data['room_id']);

            $data['specialty_ar'] = $room->room_specialty_ar;
            $data['specialty_en'] = $room->room_specialty_en;

            $this->repo->createDoctor($data);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getAllDoctors()
    {
        return $this->repo->getAllDoctors();
    }
    public function checkIfRoomsAreFull()
    {
        // احصل على جميع الغرف مع عدد الأطباء في كل غرفة
        $rooms = Room::withCount('doctors')->get();

        // إذا لم يكن هناك غرف أساساً
        if ($rooms->isEmpty()) {
            return true;
        }

        // تحقق إذا كانت جميع الغرف ممتلئة (عدد الأطباء >= السعة القصوى)
        return $rooms->every(function ($room) {
            return $room->doctors_count >= ($room->room_capacity ?? 1); // استخدم 1 كقيمة افتراضية إذا لم تكن السعة محددة
        });
    }

}
