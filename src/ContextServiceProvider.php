<?php

namespace Iotronlab\FilamentMultiGuard;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\PluginServiceProvider;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Iotronlab\FilamentMultiGuard\Http\Middleware\ApplyContext;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

abstract class ContextServiceProvider extends PluginServiceProvider
{
    protected array $livewireComponents = [];

    public function packageRegistered(): void
    {
        $this->app->booting(function () {
            $this->registerComponents();
        });

        $this->app->resolving('filament', function () {
            Filament::addContext(static::$name);

            Filament::forContext(
                static::$name,
                function () {
                    Filament::registerPages($this->getPages());
                    Filament::registerResources($this->getResources());
                    Filament::registerWidgets($this->getWidgets());

                    Filament::serving(
                        function () {
                            if (Filament::currentContext() !== static::$name) {
                                return;
                            }

                            Filament::registerUserMenuItems($this->getUserMenuItems());
                            Filament::registerScripts($this->getBeforeCoreScripts(), true);
                            Filament::registerScripts($this->getScripts());
                            Filament::registerStyles($this->getStyles());
                            Filament::registerScriptData($this->getScriptData());
                        }
                    );
                }
            );
        });
    }

    public function bootingPackage()
    {
        Filament::setContext(static::$name);

        $this->bootRoutes();
    }

    protected function bootRoutes()
    {
        if (! ($this->app instanceof CachesRoutes && $this->app->routesAreCached())) {
            Route::domain($this->contextConfig('domain'))
                ->middleware(array_merge([ApplyContext::class.':'.static::$name], $this->contextConfig('middleware.base')))
                ->name(static::$name.'.')
                ->group(function () {
                    Route::prefix($this->contextConfig('path'))->group(
                        function () {
                            $loginPage = $this->contextConfig('auth.pages.login');

                            if ($loginPage) {
                                Route::get('/login', $loginPage)->name('auth.login');
                                $guard = $this->contextConfig('auth.guard');
                                Route::post(
                                    '/logout',
                                    function (Request $request) use ($guard) {
                                        Auth::guard($guard)->logout();
                                        $request->session()->invalidate();
                                        $request->session()->regenerateToken();

                                        return redirect()->route(static::$name.'.auth.login');
                                    }
                                )->name('logout');
                            }
                            Route::middleware($this->contextConfig('middleware.auth'))->group($this->componentRoutes());
                        }
                    );
                });
        }
    }

    protected function componentRoutes(): callable
    {
        return function () {
            Route::name('pages.')->group(
                function (): void {
                    foreach (Filament::getPages() as $page) {
                        Route::group([], $page::getRoutes());
                    }
                }
            );

            Route::name('resources.')->group(
                function (): void {
                    foreach (Filament::getResources() as $resource) {
                        Route::group([], $resource::getRoutes());
                    }
                }
            );
        };
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        Filament::setContext();

        $this->bootLivewireComponents();
    }

    /**
     * @throws ReflectionException
     */
    protected function registerComponents(): void
    {
        $this->pages = $this->contextConfig('pages.register') ?? [];
        $this->resources = $this->contextConfig('resources.register') ?? [];
        $this->widgets = $this->contextConfig('widgets.register') ?? [];

        $directory = $this->contextConfig('livewire.path');
        $namespace = $this->contextConfig('livewire.namespace');

        $this->registerComponentsFromDirectory(
            Page::class,
            $this->pages,
            $this->contextConfig('pages.path'),
            $this->contextConfig('pages.namespace'),
        );

        $this->registerComponentsFromDirectory(
            Resource::class,
            $this->resources,
            $this->contextConfig('resources.path'),
            $this->contextConfig('resources.namespace'),
        );

        $this->registerComponentsFromDirectory(
            Widget::class,
            $this->widgets,
            $this->contextConfig('widgets.path'),
            $this->contextConfig('widgets.namespace'),
        );

        $filesystem = app(Filesystem::class);

        if (! $filesystem->isDirectory($directory)) {
            return;
        }
        foreach ($filesystem->allFiles($directory) as $file) {
            $fileClass = (string) Str::of($namespace)
                ->append('\\', $file->getRelativePathname())
                ->replace(['/', '.php'], ['\\', '']);

            if ((new ReflectionClass($fileClass))->isAbstract()) {
                continue;
            }

            $filePath = Str::of($directory.'/'.$file->getRelativePathname());

            if ($filePath->startsWith($this->contextConfig('resources.path')) && is_subclass_of($fileClass, Resource::class)) {
                $this->resources[] = $fileClass;

                continue;
            }

            if ($filePath->startsWith($this->contextConfig('pages.path')) && is_subclass_of($fileClass, Page::class)) {
                $this->pages[] = $fileClass;

                continue;
            }

            if ($filePath->startsWith($this->contextConfig('widgets.path')) && is_subclass_of($fileClass, Widget::class)) {
                $this->widgets[] = $fileClass;

                continue;
            }

            if (is_subclass_of($fileClass, RelationManager::class)) {
                continue;
            }

            if (! is_subclass_of($fileClass, Component::class)) {
                continue;
            }

            $livewireAlias = Str::of($fileClass)
                ->after($namespace.'\\')
                ->replace(['/', '\\'], '.')
                ->prepend(static::$name.'.')
                ->explode('.')
                ->map([Str::class, 'kebab'])
                ->implode('.');

            $this->livewireComponents[$livewireAlias] = $fileClass;
        }
    }

    protected function registerComponentsFromDirectory(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        if (Str::of($directory)->startsWith($this->contextConfig('livewire.path'))) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($directory)) {
            return;
        }

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace): string {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
            ->all(),
        );
    }

    protected function contextConfig(string $key, string $default = null)
    {
        return Arr::get(config(static::$name), $key, $default);
    }

    protected function bootLivewireComponents(): void
    {
        foreach ($this->livewireComponents as $alias => $class) {
            Livewire::component($alias, $class);
        }
    }
}
