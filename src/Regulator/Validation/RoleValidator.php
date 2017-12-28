<?php namespace Jameron\Regulator\Validation;

class RoleValidator extends Validator
{

    /**
     * Validation rules for creating new Jameron\Regulator\Models\Role models.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];
}
