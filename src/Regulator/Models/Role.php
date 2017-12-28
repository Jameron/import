<?php namespace Jameron\Regulator\Models;

use Illuminate\Database\Eloquent\Model;
use Jameron\Regulator\Models\Permission;
use Jameron\Model\Traits\PublishedTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    const USER  = 1;
    const ADMIN = 2;

    protected $table = 'regulator_roles';

    /**
     * Values that can be automatically filled.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'level',
    ];

    protected $table_prefix;

    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        $this->table_prefix = config('regulator.db_table_prefix');
        if (!empty($this->table_prefix)) {
            $this->table_prefix = $this->table_prefix . (substr($this->table_prefix, -1)=='_') ? '' : '_';
            $this->table = $this->table_prefix . $this->table;
        }
    }

    /**
     * Roles can belong to many different permissions
     *
     * @return Jameron\Regulator\Models\Permission
     */
    public function permissions()
    {
        return $this->belongsToMany('Jameron\Regulator\Models\Permission', $this->table_prefix . 'regulator_permission_role');
    }

    /**
     * Assign a role a permission
     *
     * @return Jameron\Regulator\Models\Permission
     */
    public function givePermissionTo(Permission $permission)
    {
        $this->permissions()->save($permission);
        return $this;
    }

    /**
     * Roles can belong to many different users
     *
     * @return Daylight\Regulator\Models\User
     */
    public function users()
    {
        return $this->belongsToMany(config('regulator.user.model'), $this->table_prefix . 'regulator_role_user');
    }

    /**
     * Assign an user a role
     *
     * @param string $role
     * @return Jameron\Regulator\Models\Role
     */
    public function assignUser($user)
    {
        $this->users()->save($user);
        return $this;
    }
}
