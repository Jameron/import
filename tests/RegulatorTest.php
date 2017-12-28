<?php
use PHPUnit\Framework\TestCase;
use Jameron\Regulator\Regulator;
use Illuminate\Support\MessageBag;
use Jameron\Regulator\Validation\RoleValidator;
use Jameron\Regulator\Validation\PermissionValidator;
use Jameron\Regulator\Repositories\RegulatorRepository;
use Jameron\Regulator\Models\User;

require_once 'vendor/autoload.php';

class RegulatorTest extends TestCase
{

    private $regulator;

	use DatabaseTransactions;

	public function setUp()
	{
		parent::setUp();

		$this->regulator = new Regulator(new RegulatorRepository(new RoleValidator, new PermissionValidator), new MessageBag());
	}

	public function tearDown()
	{
		parent::tearDown();
        $this->r = null;
	}

	public function testCreateRoleByName()
	{
		$role = $this->regulator->createRole('Test');

		$this->assertEquals('Test', $role->name);
	}

	public function testGetRoleById()
	{
		$role = $this->regulator->createRole('Test');

		$get_role = $this->regulator->role($role->id);

		$this->assertEquals('Test', $get_role->name);
	}

	public function testGetAllRoles()
	{
		$role1 = $this->regulator->createRole('Test1');
		$role2 = $this->regulator->createRole('Test2');
		$role3 = $this->regulator->createRole('Test3');

		$all_roles = $this->regulator->roles();
		$this->assertEquals('Test1', $all_roles->get(0)->name);
		$this->assertEquals('Test2', $all_roles->get(1)->name);
		$this->assertEquals('Test3', $all_roles->get(2)->name);

	}

	public function testCreatePermissionByName()
	{
		$permission = $this->regulator->createPermission('Test');

		$this->assertEquals('Test', $permission->name);
	}

	public function testGetPermissionById()
	{
		$permission = $this->regulator->createPermission('Test');
		$get_permission = $this->regulator->permission($permission->id);
		$this->assertEquals('Test', $get_permission->name);
	}

	public function testGetAllPermissions()
	{
		$permission1 = $this->regulator->createPermission('Test1');
		$permission2 = $this->regulator->createPermission('Test2');
		$permission3 = $this->regulator->createPermission('Test3');

		$all_permissions = $this->regulator->permissions();
		$this->assertEquals('Test1', $all_permissions->get(0)->name);
		$this->assertEquals('Test2', $all_permissions->get(1)->name);
		$this->assertEquals('Test3', $all_permissions->get(2)->name);
	}

	public function testGivePermissionToRole() 
	{
		$role = $this->regulator->createRole('Test');
		$permission = $this->regulator->createPermission('TestPermission');
		$result = $role->givePermissionTo($permission);
		$this->assertEquals($permission->name, $role->permissions->get(0)->name);
		
	}

	public function testAssignRole() 
	{
		$user = User::create([
							'first'	=>	'cameron',
							'last'	=>	'macfarlane',
							'email'	=>	'cammac1984@gmail.com',
							'password' => 'QWE!@#'
						]);

		$role = $this->regulator->createRole('Test');
		$user->assignRole($role->name);
		$this->assertEquals($role->name, $user->roles->get(0)->name);
		
	}

	public function testHasRole() 
	{

		$user = User::create([
							'first'	=>	'jon',
							'last'	=>	'doe',
							'email'	=>	'jondoe@example.com',
							'password' => 'QWE!@#'
						]);

		$role = $this->regulator->createRole('user');
		$user->assignRole($role->name);
		$this->assertTrue($user->hasRole($role->name));
		$this->assertFalse($user->hasRole('admin'));
		
	}


	public function testGetRoleUsers() 
	{

		$user1 = User::create([
							'first'	=>	'jon',
							'last'	=>	'doe',
							'email'	=>	'jondoe@example.com',
							'password' => 'QWE!@#'
						]);

		$user2 = User::create([
							'first'	=>	'jane',
							'last'	=>	'doe',
							'email'	=>	'janedoe@example.com',
							'password' => 'QWE!@#'
						]);

		$role = $this->regulator->createRole('Admin');

		$user1->assignRole($role->name);
		$user2->assignRole($role->name);

		$this->assertEquals($user1->first, $role->users->get(0)->first);
		$this->assertEquals($user2->first, $role->users->get(1)->first);
		
	}

	public function testCanPermission()
	{

		// Create the admin user, role, and permission to view admin dashboard
		$admin_user = User::create([
							'first'	=>	'jon',
							'last'	=>	'doe',
							'email'	=>	'jondoe@example.com',
							'password' => 'QWE!@#'
						 ]);

		$permission = $this->regulator->createPermission('view_admin_dashboard');
		$role = $this->regulator->createRole('admin')->givePermissionTo($permission);
		$admin_user->assignRole($role->name);
		$admin_user_seen_admin_dash = false;
		$admin_user_seen_user_dash = false;

		Gate::define($permission->name, function ($user) use ($permission) {
			return $user->hasRole($permission->roles);
		});

		// Create a permission and role. Add permission to role. Create user, assign role to user.
		$user_permission = $this->regulator->createPermission('view_user_dashboard');
		$user_role = $this->regulator->createRole('user')->givePermissionTo($user_permission);

		$user = User::create([
							'first'		=> 'joe',
							'last'		=> 'blow',
							'email'		=> 'joeblow@gmail.com',
							'password' 	=> 'QWE!@#'
						 ]);

		$user->assignRole($user_role->name);

		$user_seen_admin_dash = false;
		$user_seen_user_dash = false;

		Gate::define($user_permission->name, function ($user) use ($user_permission) {
			return $user->hasRole($user_permission->roles);
		});

		if ($admin_user->can('view_admin_dashboard', $admin_user)) {
			$admin_user_seen_admin_dash = true;
		}

		if ($admin_user->can('view_user_dashboard', $admin_user)) {
			$admin_user_seen_user_dash = true;
		}

		if ($user->can('view_admin_dashboard', $user)) {
			$user_seen_admin_dash = true;
		}

		if ($user->can('view_user_dashboard', $user)) {
			$user_seen_user_dash = true;
		}

		$this->assertTrue($admin_user_seen_admin_dash);
		$this->assertFalse($admin_user_seen_user_dash);
		$this->assertTrue($user_seen_user_dash);
		$this->assertFalse($user_seen_admin_dash);
	}

}
