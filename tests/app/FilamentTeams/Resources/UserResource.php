<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources;

use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Iotronlab\FilamentMultiGuard\Concerns\ContextualResource;
use Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource\Pages\CreateUser;
use Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource\Pages\EditUser;
use Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource\Pages\ListUsers;
use Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Resources\UserResource\RelationManagers\PostsRelationManager;
use Iotronlab\FilamentMultiGuard\Tests\app\Models\User;

class UserResource extends Resource
{

    use ContextualResource;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $slug = 'teams-users';

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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
