<?php

namespace App\Repositories\Eloquent\Api\Doctor;

use App\Models\Doctor;
use App\Repositories\Api\Doctor\DoctorRepositoryInterface;

class DoctorRepository implements DoctorRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function getAllDoctors($lang)
    {
        $doctors = Doctor::with(['user', 'doctorProfile'])
            ->paginate(5);

        // الترتيب بعد الجلب حسب اللغة
        return $doctors->getCollection()
            ->sortBy(function ($doctor) use ($lang) {
                return optional($doctor->doctorProfile)->{"specialist_$lang"};
            })
            ->values(); // إعادة فهرسة
    }
    public function findDoctorWithSchedules($id, $lang){
        return Doctor::with(['user', 'doctorProfile', 'doctorSchedule'])
        ->find($id);
    }
    public function findById($id)
    {
        return Doctor::with(['user', 'doctorProfile', 'doctorSchedule'])->findOrFail($id);
    }

}
