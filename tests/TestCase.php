<?php

namespace Iotronlab\FilamentMultiGuard\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Iotronlab\FilamentMultiGuard\FilamentMultiGuardServiceProvider;
use Iotronlab\FilamentMultiGuard\Tests\app\Providers\FilamentTeamsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

//        Factory::guessFactoryNamesUsing(
//            fn (string $modelName) => 'Iotronlab\\FilamentMultiGuard\\Database\\Factories\\'.class_basename($modelName).'Factory'
//        );
    }

    protected function getPackageProviders($app)
    {
        return array_filter(array_merge([
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
//            SpatieLaravelSettingsPluginServiceProvider::class,
//            SpatieLaravelTranslatablePluginServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,

        ],$this->getCorePackageProviders($app)),function ($class){
            return class_exists($class);
        });
    }

    protected function getCorePackageProviders($app)
    {
        return [
            FilamentMultiGuardServiceProvider::class,
            FilamentTeamsServiceProvider::class,
        ];
    }


    /**
     * Define database migrations for testing.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Load Configs
     * @param $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app['config']->set('filament', require __DIR__ . '/config/filament.php');

        $app['config']->set('filament-teams', require __DIR__ . '/config/filament-teams.php');


        /*
        $migration = include __DIR__.'/../database/migrations/create_filament-multi-guard_table.php.stub';
        $migration->up();
        */
    }
}
