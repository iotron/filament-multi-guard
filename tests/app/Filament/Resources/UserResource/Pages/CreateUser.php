<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
