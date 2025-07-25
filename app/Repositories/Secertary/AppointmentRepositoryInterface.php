<?php

namespace App\Repositories\Secertary;

interface AppointmentRepositoryInterface
{
    public function getPendingByDoctor($doctorId);


public function updateStatus($appointmentId, $status);
 public function fetchConfirmedAppointmentsToday();
 public function deleteOldPendingAppointments();
}
