<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Define all available permissions.
     */
    protected array $permissions = [
        'view clients',
        'create clients',
        'edit clients',
        'delete clients',
        'export clients',
    ];

    /**
     * Role-specific permissions.
     */
    protected array $rolePermissions = [
        RolesEnum::ADMINISTRATOR->value => 'all',
        RolesEnum::ACCOUNT_EXECUTIVE->value => [
            'view clients',
            'export clients',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create all permissions if they don't exist
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        foreach ($this->rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            $permissionsToAssign = $permissions === 'all'
                ? Permission::all()
                : Permission::whereIn('name', $permissions)->get();

            $role->syncPermissions($permissionsToAssign);
        }
    }
}
