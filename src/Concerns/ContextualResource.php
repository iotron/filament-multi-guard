<?php

namespace Iotronlab\FilamentMultiGuard\Concerns;

use Filament\Facades\Filament;

trait ContextualResource
{
    public static function getRouteBaseName(): string
    {
        $slug = static::getSlug();

        return Filament::currentContext().".resources.{$slug}";
    }
}
