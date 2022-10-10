<?php

namespace Iotronlab\FilamentMultiGuard;

use Iotronlab\FilamentMultiGuard\Commands\MakeContextGuardCommand;
use Iotronlab\FilamentMultiGuard\Http\Middleware\ApplyContext;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Iotronlab\FilamentMultiGuard\Commands\FilamentMultiGuardCommand;

class FilamentMultiGuardServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-multi-guard')
            ->hasConfigFile()
            ->hasCommand(MakeContextGuardCommand::class);
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
