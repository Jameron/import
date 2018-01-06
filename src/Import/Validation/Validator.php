<?php namespace Jameron\Import\Validation;

use Validator as V;

abstract class Validator
{

    /**
     * The list of validation errors.
     *
     * @var Illuminate\Support\MessageBag
     */
    public $errors;

    /**
     * Model validator for creating new model objects.
     *
     * @param array $input
     * @return boolean
     */
    public function validate($input)
    {
        $validator = V::make($input, static::$rules);
        
        if ($validator->fails()) {
            $this->errors = $validator->messages();
            return false;
        }

        return true;
    }

    /**
     *
     * @return $errors
     */
    public function errors()
    {
        return $this->errors;
    }
}
