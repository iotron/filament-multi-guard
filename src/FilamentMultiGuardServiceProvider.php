<?php

namespace Iotronlab\FilamentMultiGuard;

use Iotronlab\FilamentMultiGuard\Commands\FilamentContextCommand;
use Iotronlab\FilamentMultiGuard\Commands\FilamentGuardCommand;
use Iotronlab\FilamentMultiGuard\Http\Middleware\ApplyContext;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMultiGuardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-multi-guard')
            ->hasCommands([
                FilamentContextCommand::class,
                FilamentGuardCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->extend('filament', function ($service, $app) {
            return new FilamentMultiGuard($service);
        });
    }

    public function packageBooted(): void
    {
        Livewire::addPersistentMiddleware([
            ApplyContext::class,
        ]);
    }
}
