<?php

namespace App\Repositories\Api\Doctor;

interface DoctorRepositoryInterface
{
    public function getAllDoctors($lang);
    public function findDoctorWithSchedules($id, $lang);
    public function findById($id);
}
