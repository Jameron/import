<?php

namespace Jameron\Regulator\Repositories;

use DB;
use Jameron\Regulator\RoleInput;
use Jameron\Regulator\Models\Role;
use Jameron\Regulator\PermissionInput;
use Jameron\Regulator\Models\Permission;
use Jameron\Regulator\Validation\RoleValidator;
use Jameron\Regulator\Validation\PermissionValidator;

class RegulatorRepository
{

    /**
     * Model validator for creating new model objects.
     *
     * @param array $input
     * @return boolean
     */
    protected $validator;

    /**
     * The list of role validation rules.
     *
     * @return array
     */
    protected $rValidator;
    
    /**
     * Constructor for RegulatorRepository.
     *
     * @return void
     */
    public function __construct(RoleValidator $roleValidator, PermissionValidator $permissionValidator)
    {
        $this->rValidator = $roleValidator;
        $this->pValidator = $permissionValidator;
    }
    
    /**
     * Inserts and returns a new Role model object.
     *
     * @param  array|string $values
     * @return Jameron\Regulator\Models\Role
     */
    public function createRole($values)
    {
        $formatter = new RoleInput();

        $values = $formatter->output($values);
        if ($this->rValidator->validate($values)) {
            return $role = Role::create($values);
        } else {
            return $this->rValidator->errors;
        }
    }

    /**
     * Returns a Role model object.
     *
     * @param  integer $id
     * @return Jameron\Regulator\Models\Role
     */
    public function role($id)
    {
        return Role::find($id);
    }

    /**
     * Returns a collection of Role model objects.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function roles()
    {
        return Role::all();
    }

    /**
     * Inserts and returns a new Role model object.
     *
     * @param  array|string $values
     * @return Jameron\Regulator\Models\Role
     */
    public function createPermission($values)
    {
        $formatter = new PermissionInput();

        $values = $formatter->output($values);
        if ($this->pValidator->validate($values)) {
            return $role = Permission::create($values);
        } else {
            return $this->pValidator->errors;
        }
    }

    /**
     * Returns a Role model object.
     *
     * @param  integer $id
     * @return Jameron\Regulator\Models\Permission
     */
    public function permission($id)
    {
        return Permission::find($id);
    }

    /**
     * Returns a collection of Role model objects.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function permissions()
    {
        return Permission::all();
    }
}
