<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = [
            ['name' => 'super_admin',
                'guard_name' => 'web',
                'permissions' => ['ViewAny:Role', 'View:Role', 'Create:Role', 'Update:Role',
                    'Delete:Role', 'Restore:Role', 'ForceDelete:Role', 'ForceDeleteAny:Role',
                    'RestoreAny:Role', 'Replicate:Role', 'Reorder:Role', 'ViewAny:Category',
                    'View:Category', 'Create:Category', 'Update:Category', 'Delete:Category',
                    'Restore:Category', 'ForceDelete:Category', 'ForceDeleteAny:Category', 'RestoreAny:Category',
                    'Replicate:Category', 'Reorder:Category', 'ViewAny:ProductCollection', 'View:ProductCollection',
                    'Create:ProductCollection', 'Update:ProductCollection', 'Delete:ProductCollection', 'Restore:ProductCollection',
                    'ForceDelete:ProductCollection', 'ForceDeleteAny:ProductCollection', 'RestoreAny:ProductCollection', 'Replicate:ProductCollection',
                    'Reorder:ProductCollection', 'ViewAny:ProductVariant', 'View:ProductVariant', 'Create:ProductVariant',
                    'Update:ProductVariant', 'Delete:ProductVariant', 'Restore:ProductVariant', 'ForceDelete:ProductVariant',
                    'ForceDeleteAny:ProductVariant', 'RestoreAny:ProductVariant', 'Replicate:ProductVariant', 'Reorder:ProductVariant',
                    'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'Restore:Product',
                    'ForceDelete:Product', 'ForceDeleteAny:Product', 'RestoreAny:Product', 'Replicate:Product', 'Reorder:Product',
                    'ViewAny:Order', 'View:Order', 'ViewAny:UserInfoEntry', 'View:UserInfoEntry',
                    'Create:UserInfoEntry', 'Update:UserInfoEntry','Delete:UserInfoEntry', 'Replicate:UserInfoEntry',
                    'ViewAny:Page', 'View:Page', 'Create:Page', 'Update:Page', 'Delete:Page', 'Restore:Page',
                    'ForceDelete:Page', 'ForceDeleteAny:Page', 'RestoreAny:Page', 'Replicate:Page', 'Reorder:Page',
                    'ViewAny:Section', 'View:Section', 'Create:Section', 'Update:Section', 'Delete:Section', 'Restore:Section',
                    'ForceDelete:Section', 'ForceDeleteAny:Section', 'RestoreAny:Section', 'Replicate:Section', 'Reorder:Section',

                ]], ['name' => 'customer', 'guard_name' => 'web',
                    'permissions' => [
                        'ViewAny:Order', 'View:Order',
                        'ViewAny:UserInfoEntry', 'View:UserInfoEntry',
                        'Create:UserInfoEntry', 'Update:UserInfoEntry',
                        'Delete:UserInfoEntry', 'Replicate:UserInfoEntry']]];

        $directPermissions = '[]';

        $rolesWithPermissions = json_encode($rolesWithPermissions); // preg_replace('/\s+/', '', $rolesWithPermissions);

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
