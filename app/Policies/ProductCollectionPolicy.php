<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProductCollection;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProductCollectionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductCollection');
    }

    public function view(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('View:ProductCollection');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductCollection');
    }

    public function update(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('Update:ProductCollection');
    }

    public function delete(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('Delete:ProductCollection');
    }

    public function restore(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('Restore:ProductCollection');
    }

    public function forceDelete(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('ForceDelete:ProductCollection');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductCollection');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductCollection');
    }

    public function replicate(AuthUser $authUser, ProductCollection $productCollection): bool
    {
        return $authUser->can('Replicate:ProductCollection');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductCollection');
    }
}
