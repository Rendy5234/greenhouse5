<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SensorReading;
use Illuminate\Auth\Access\HandlesAuthorization;

class SensorReadingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SensorReading');
    }

    public function view(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('View:SensorReading');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SensorReading');
    }

    public function update(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('Update:SensorReading');
    }

    public function delete(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('Delete:SensorReading');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SensorReading');
    }

    public function restore(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('Restore:SensorReading');
    }

    public function forceDelete(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('ForceDelete:SensorReading');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SensorReading');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SensorReading');
    }

    public function replicate(AuthUser $authUser, SensorReading $sensorReading): bool
    {
        return $authUser->can('Replicate:SensorReading');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SensorReading');
    }

}