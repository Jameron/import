<?php namespace Jameron\Regulator\Facades;

use Illuminate\Support\Facades\Facade;

class RegulatorFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'regulator';
    }
}
