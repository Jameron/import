<?php

namespace Jameron\Regulator\database\seeds;

use Illuminate\Database\Seeder;

class RegulatorSeeder extends Seeder
{
    public function run()
    {
        // Create an admin role
        $user_model = config('regulator.user.model');

        $admin_user = factory($user_model, 'admin')->create();
        $user = factory($user_model, 'user')->create();

        $admin_role = factory(\Jameron\Regulator\Models\Role::class, 'admin')->create();
        $user_role = factory(\Jameron\Regulator\Models\Role::class, 'user')->create();

        $admin_role->assignUser($admin_user);
        $user_role->assignUser($user);

        $view_admin_dashboard_permission = factory(\Jameron\Regulator\Models\Permission::class, 'view_admin_dashboard')->create();
        $view_user_dashboard_permission = factory(\Jameron\Regulator\Models\Permission::class, 'view_user_dashboard')->create();
        $edit_account_info_permission = factory(\Jameron\Regulator\Models\Permission::class, 'edit_account_info')->create();

        // User permissions
        $create_users_permission = factory(\Jameron\Regulator\Models\Permission::class, 'create_users')->create();
        $read_users_permission = factory(\Jameron\Regulator\Models\Permission::class, 'read_users')->create();
        $update_users_permission = factory(\Jameron\Regulator\Models\Permission::class, 'update_users')->create();
        $delete_users_permission = factory(\Jameron\Regulator\Models\Permission::class, 'delete_users')->create();

        // Role permissions
        $create_roles_permission = factory(\Jameron\Regulator\Models\Permission::class, 'create_roles')->create();
        $read_roles_permission = factory(\Jameron\Regulator\Models\Permission::class, 'read_roles')->create();
        $update_roles_permission = factory(\Jameron\Regulator\Models\Permission::class, 'update_roles')->create();
        $delete_roles_permission = factory(\Jameron\Regulator\Models\Permission::class, 'delete_roles')->create();

        // Permission permissions
        $create_permissions_permission = factory(\Jameron\Regulator\Models\Permission::class, 'create_permissions')->create();
        $read_permissions_permission = factory(\Jameron\Regulator\Models\Permission::class, 'read_permissions')->create();
        $update_permissions_permission = factory(\Jameron\Regulator\Models\Permission::class, 'update_permissions')->create();
        $delete_permissions_permission = factory(\Jameron\Regulator\Models\Permission::class, 'delete_permissions')->create();
        
        // Manage users
        $admin_role->givePermissionTo($create_users_permission);
        $admin_role->givePermissionTo($read_users_permission);
        $admin_role->givePermissionTo($update_users_permission);
        $admin_role->givePermissionTo($delete_users_permission);
        // Manage roles
        $admin_role->givePermissionTo($create_roles_permission);
        $admin_role->givePermissionTo($read_roles_permission);
        $admin_role->givePermissionTo($update_roles_permission);
        $admin_role->givePermissionTo($delete_roles_permission);
        // Manage permissions 
        $admin_role->givePermissionTo($create_permissions_permission);
        $admin_role->givePermissionTo($read_permissions_permission);
        $admin_role->givePermissionTo($update_permissions_permission);
        $admin_role->givePermissionTo($delete_permissions_permission);

        // Admin user permissions
        $admin_role->givePermissionTo($view_admin_dashboard_permission);
        $admin_role->givePermissionTo($edit_account_info_permission);

        // User permissions
        $user_role->givePermissionTo($view_user_dashboard_permission);
        $user_role->givePermissionTo($edit_account_info_permission);

        return $this;
    }
}
