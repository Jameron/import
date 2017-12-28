<?php namespace Jameron\Import\Validation;

class ImportValidator extends Validator
{

    /**
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];
}
