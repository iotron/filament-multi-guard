<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource\Pages;

use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
