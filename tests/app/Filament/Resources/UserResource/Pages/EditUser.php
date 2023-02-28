<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\Pages;

use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
