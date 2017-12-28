<?php

namespace Jameron\Regulator\Contracts;

interface CreateNewInputInterface
{

    /**
     * Return the name value formatted array of attributes
     *
     * @param  array  $input
     * @param  integer $options
     * @return array
     */
    public function output($input);
}
