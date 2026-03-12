<?php

namespace App\Listeners;

use Filament\Auth\Events\Registered;
use Filament\Facades\Filament;
use Spatie\Permission\Models\Role;

class AssignCustomerRoleToUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        if (Filament::getCurrentPanel()->getId() === 'customer') {
            $role = Role::findByName('customer');
            $user = $event->getUser();
            $user->assignRole($role);
        }
    }
}
