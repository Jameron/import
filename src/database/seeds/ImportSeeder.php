<?php

namespace Jameron\Import\database\seeds;

use Illuminate\Database\Seeder;

class ImportSeeder extends Seeder
{
    public function run()
    {
		$upload_csv_permission = factory(\Jameron\Regulator\Models\Permission::class, 'upload_csv')->create();
	    $admin_role = \Jameron\Regulator\Models\Role::where ('slug', 'admin')->first();
        $admin_role->givePermissionTo($upload_csv_permission);
	}
}
