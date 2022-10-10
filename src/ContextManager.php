<?php

namespace Iotronlab\FilamentMultiGuard;

use Filament\FilamentManager;
use Illuminate\Contracts\Auth\Guard;

class ContextManager extends FilamentManager
{
    public static ?string $config = null;

    public static function getAuth(): ?Guard
    {
        return static::$config ? auth()->guard(config(static::$config . 'auth.guard')) : null;
    }

    public function auth(): Guard
    {
        return static::getAuth() ?? auth()->guard(config('filament.auth.guard'));
    }
}
