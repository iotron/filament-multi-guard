<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources;

use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\Pages\CreateUser;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\Pages\EditUser;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\Pages\ListUsers;
use Iotronlab\FilamentMultiGuard\Tests\app\Filament\Resources\UserResource\RelationManagers\PostsRelationManager;
use Iotronlab\FilamentMultiGuard\Tests\app\Models\User;

class UserResource extends Resource
{

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            PostsRelationManager::class,
        ];
    }


}
