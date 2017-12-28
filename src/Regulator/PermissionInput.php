<?php

namespace Jameron\Regulator;

use Jameron\Regulator\Contracts\CreateNewInputInterface;
use Jameron\Regulator\Models\Permission;

class PermissionInput implements CreateNewInputInterface
{

    /**
     * Returns formatted array of name values for inserting a new Role model object.
     *
     * @param  string|array  $values
     * @param  integer $depth
     * @return array
     */
    public function output($values)
    {
        // If a string convert to an array
        $values = (is_string($values)) ? [$values] : $values;

        // If is array proceed
        if (is_array($values)) {

            // Create a new role model, then get the column names from the associated Role table
            $permission = new Permission();

            $modelColumnsArray = $permission->columnList()->toArray();
            array_shift($modelColumnsArray);

            // If user passed in a name value array then process it by matching column
            // data to column name
            if (array_key_exists('name', $values)) {
                $values = array_intersect_key($values, array_flip($modelColumnsArray));
            } else {
                $values = array_combine(array_intersect_key($modelColumnsArray, $values), array_intersect_key($values, $modelColumnsArray));
            }
            // Generate a slug value using the name attribute and str_slug function
            if (isset($values['name'])) {
                $values['slug'] = (!isset($values['slug'])) ? str_slug($values['name'], '-') : $values['slug'];

                // return array of name value attributes for validation to process
                return $values;
            }

            return [];
        }
    }
}
