<?php

namespace App\Repositories\Admin;

interface DashboardRepositoryInterface
{
    public function getLatestSecretary();
    public function secretaryExists();
    public function createSecretaryUser(array $data);
    public function findSecretaryById(int $id);
    public function updateSecretary($secretary, array $data);
    public function updateSecretaryProfile($secretary, array $data);
    public function getAvailableRooms(string $language);
     public function createDoctor(array $data);
    public function checkRoomCapacity(int $roomId): bool;
    public function getRoomById(int $roomId);
    public function getAllDoctors();
}
