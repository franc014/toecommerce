<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\UserInfoEntry;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserInfoEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserInfoEntry');
    }

    public function view(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('View:UserInfoEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserInfoEntry');
    }

    public function update(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Update:UserInfoEntry');
    }

    public function delete(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Delete:UserInfoEntry');
    }

    public function restore(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Restore:UserInfoEntry');
    }

    public function forceDelete(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('ForceDelete:UserInfoEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserInfoEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserInfoEntry');
    }

    public function replicate(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Replicate:UserInfoEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserInfoEntry');
    }
}
