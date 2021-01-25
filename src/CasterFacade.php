<?php

namespace Morrislaptop\Caster;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Morrislaptop\Caster\Caster
 */
class CasterFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-castable-object';
    }
}
