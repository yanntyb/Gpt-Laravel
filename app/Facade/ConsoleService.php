<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class ConsoleService extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'consoleservice';
    }
}
