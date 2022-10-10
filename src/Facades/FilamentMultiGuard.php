<?php

namespace Iotronlab\FilamentMultiGuard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Iotronlab\FilamentMultiGuard\FilamentMultiGuard
 */
class FilamentMultiGuard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Iotronlab\FilamentMultiGuard\FilamentMultiGuard::class;
    }
}
