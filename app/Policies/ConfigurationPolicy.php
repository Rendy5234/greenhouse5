<?php

namespace App\Policies;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConfigurationPolicy
{
    /**
     * Tentukan apakah user bisa melihat daftar konfigurasi.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('view_any configuration')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa melihat detail konfigurasi.
     */
    public function view(User $user, Configuration $configuration): Response
    {
        return $user->can('view configuration')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa membuat konfigurasi baru.
     */
    public function create(User $user): Response
    {
        return $user->can('create configuration')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa mengupdate konfigurasi.
     */
    public function update(User $user, Configuration $configuration): Response
    {
        return $user->can('update configuration')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }

    /**
     * Tentukan apakah user bisa menghapus konfigurasi.
     */
    public function delete(User $user, Configuration $configuration): Response
    {
        return $user->can('delete configuration')
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses.');
    }
}
