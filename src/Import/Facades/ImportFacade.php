<?php namespace Jameron\Import\Facades;

use Illuminate\Support\Facades\Facade;

class ImportFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Import';
    }
}
