<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserInfoEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserInfoEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserInfoEntry');
    }

    public function view(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('View:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserInfoEntry');
    }

    public function update(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Update:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
    }

    public function delete(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Delete:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
    }

    public function restore(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('Restore:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
    }

    public function forceDelete(AuthUser $authUser, UserInfoEntry $userInfoEntry): bool
    {
        return $authUser->can('ForceDelete:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
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
        return $authUser->can('Replicate:UserInfoEntry') && $authUser->id === $userInfoEntry->user_id;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserInfoEntry');
    }

}
