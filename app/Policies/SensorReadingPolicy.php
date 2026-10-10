<?php

namespace App\Policies;

use App\Models\SensorReading;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SensorReadingPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar reading sensor.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('view_any sensor_reading')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa melihat detail reading sensor.
     */
    public function view(User $user, SensorReading $sensorReading): Response
    {
        return $user->can('view sensor_reading')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa membuat reading sensor baru.
     */
    public function create(User $user): Response
    {
        return $user->can('create sensor_reading')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa mengupdate reading sensor.
     */
    public function update(User $user, SensorReading $sensorReading): Response
    {
        return $user->can('update sensor_reading')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa menghapus reading sensor.
     */
    public function delete(User $user, SensorReading $sensorReading): Response
    {
        return $user->can('delete sensor_reading')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
