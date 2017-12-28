<?php namespace Jameron\Regulator\Models\Traits;

use Jameron\Regulator\Models\Role;

trait HasRoles
{

    /*
    |--------------------------------------------------------------------------
    | HasRoles Trait
    |--------------------------------------------------------------------------
    |
    | This class handles the user model relationships that relate to roles and
    | permissions as well as adds some eloquent syntax to the default laravel
    | model methods.
    |
     */

    protected $table_prefix;

    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        $this->dates[] = 'last_login';
        $this->dates[] = 'last_logout';
        $this->table_prefix = config('regulator.db_table_prefix');
        if (!empty($this->table_prefix)) {
            $this->table_prefix = $this->table_prefix . (substr($this->table_prefix, -1)=='_') ? '' : '_';
        }
    }

    /**
     * User can belong to many different roles
     *
     * @return Jameron\Regulator\Models\Role
     */
    public function roles()
    {
        return $this->belongsToMany('Jameron\Regulator\Models\Role', $this->table_prefix . 'regulator_role_user')
                    ->orderBy('level', 'desc');
    }

    /**
     * Meaningful syntax to determine if an user has a given role
     *
     * @param string $role
     * @return Jameron\Regulator\Models\Role
     */
    public function hasRole($roles=null)
    {
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        } elseif (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->roles->contains('slug', $role)) {
                    return true;
                }
            }
        }
        if (is_object($roles)) {
            return  !! $roles->intersect($this->roles)->count();
        }
    }

    /**
     * Assign a role to an user
     *
     * @param string $role
     * @return Jameron\Regulator\Models\Role
     */
    public function assignRole($role)
    {
        $role = Role::where('slug', $role)->firstOrFail();
        $this->roles()->save($role);
        return $this;
    }
}
