# filament-multi-guard

This package allows you to register multiple filament route/path based contexts in your
application with their own set of resources, pages, widgets and guard. The contexts can also be used on the same guard instance. eg. for role based setups.

This package is derived from [filament-multi-context](https://github.com/artificertech/filament-multi-context) package but doesn't include it as a dependency as the 
package doesn't support multiple guard instances.

## Installation

You can install the package via composer:

```bash
composer require iotronlab/filament-multi-guard
```

## Single Guard Usage

Create a new filament context **with out GuardLogin and GuardMiddleware.** 

```bash
php artisan make:filament-context FilamentTeams
```

The above command will create the following files and directories:

```
app/FilamentTeams/Pages/
app/FilamentTeams/Resources/
app/FilamentTeams/Widgets/
app/Providers/FilamentTeamsServiceProvider.php
config/filament-teams.php
```

## Multi Guard Usage

To create a new context **with GuardLogin and GuardMiddleware.**

```bash
php artisan make:filament-context FilamentTeams --guard
```

or make a GuardLogin and GuardMiddleware for a already generated context

```bash
php artisan make:filament-guard FilamentTeams 
```

The above command will create the following files and directories:

```
app/FilamentTeams/Middleware/FilamentTeamsMiddleware.php
app/FilamentTeams/Pages/
app/FilamentTeams/Resources/
app/FilamentTeams/Widgets/
app/Http/Livewire/FilamentTeamsLogin.php
app/Providers/FilamentTeamsServiceProvider.php
config/filament-teams.php
```

Replace the auth guard, pages login and middleware auth in the context config with the **generated login page and middleware**.

```
use App\FilamentTeams\Middleware\FilamentTeamsMiddleware;
use App\Http\Livewire\FilamentTeamsLogin;

    'auth' => [
        'guard' => 'your-custom-guard',
        'pages' => [
            'login' => FilamentTeamsLogin::class,
        ],
    ],

     'middleware' => [
        'auth' => [
            // Authenticate::class,
            FilamentTeamsMiddleware::class
        ],
       
    ],
```

Now, you can go to /{context-path}/login to login to the new context. You can remove the ***dashboard*** from 'pages' in the context config and implement your own dashboard.

You should implement the **logout** components UserMenuItem in a service provider with Filament::serving()

```php
Filament::serving(function () {
    Filament::forContext('filament-teams', function () {
            Filament::registerUserMenuItems([
                'logout' => UserMenuItem::make()->label('Log Out')->url(route('filament-teams.logout')),
                ]);
            });
        });
```

## Adding Pages/Resources to context

`Filament` cannot be passed as a context to this command as it is reserved for
the default filament installation

> **_Register Provider:_** Be sure to add the `FilamentTeamsServiceProvider`
> class to your providers array in `config/app.php`

You may now add filament resources in your FilamentTeams directories. Generate Filament pages/resources/widgets as you normally would. Move them into the context-folder 
and update the namespace.

> **_Context Traits:_** be sure to
add the ContextualPage and ContextualResource traits to their associated classes
inside of your context directories. Without this when filament generates
navigation links it will try to use `filament.pages.*` and
`filament.resources.{resource}.*` instead of `{context}.pages.*` and
`{context}.resources.{resource}.*` as the route names

### ContextualPage & ContextualResource traits

Pages:

```php
namespace App\FilamentTeams\Resources;

use Iotronlab\FilamentMultiGuard\Concerns\ContextualPage;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use ContextualPage;
}
```

Resources:

```php
namespace App\FilamentTeams\Resources;

use Iotronlab\FilamentMultiGuard\Concerns\ContextualResource;
use Filament\Resources\Resource;

class UserResource extends Resource
{
    use ContextualResource;
}
```

## Configuration

The `config/filament-teams.php` file contains a subset of the
`config/filament.php` configuration file. The values in the `filament-teams.php`
file can be adjusted and will only affect the pages, resources, widgets, and auth guard for
the `filament-teams` context.

Currently the configuration values that can be modified for a specific context
are:

```
'path'
'domain'
'pages'
'resources'
'widgets'
'livewire'
'auth'
'middleware'
```

### ContextServiceProvider

Your `ContextServiceProvider` found in your
`app/Providers/FilamentTeamsServiceProvider.php` is an extension of the Filament
`PluginServiceProvder` so features of the `PluginServiceProvider` may be used
for your context

### Custom Page and Resource Routing

If you would like more control over the way pages and resources are routed you
may override the `componentRoutes()` function in your
`FilamentTeamsServiceProvider`

```php
protected function componentRoutes(): callable
    {
        return function () {
            Route::name('pages.')->group(function (): void {
                foreach (Facades\Filament::getPages() as $page) {
                    Route::group([], $page::getRoutes());
                }
            });

            Route::name('resources.')->group(function (): void {
                foreach (Facades\Filament::getResources() as $resource) {
                    Route::group([], $resource::getRoutes());
                }
            });
        };
    }
```

## The Filament Facade

In order for this package to work the `filament` app service has been overriden.
Each context is represented by its own `FilamentMultiGuard\ContextManager` extending `Filament\FilamentManager` object. Within
your application calls to the filament facade (such as `Filament::serving`) will
be proxied to the appropriate `Filament\FilamentManager` object based on the
current context of your application (which is determined by the route of the
request)

### Context Functions

The following functions have been added to facilitate multiple
`Filament\FilamentManger` objects in your application:

```php
// retrieve the string name of the current application context
// defaults to `filament`

Filament::currentContext(): string
```

```php
// retrieve the Filament\FilamentManager object for the current app context

Filament::getContext()
```

```php
// retrieve the array of Filament\FilamentManager objects keyed by the context name

Filament::getContexts()
```

```php
// set the current app context. 
// Passing null or nothing sets the context to 'filament'

Filament::setContext(string|null $context)
```

```php
// sets the context for the duration of the callback function, then resets it back to the original value
Filament::forContext(string $context, function () {
    // ...
})
```

```php
// loops through each registered context (including the default 'filament' context), 
// sets that context as the current context, 
// runs the callback, then resets to the original value
Filament::forAllContexts(function () {
    // ...
})
```

```php
// creates a new FilamentManager object and registers it under the $name context
// this method is used by your ContextServiceProvider to register your context
// you shouldn't need to use this method during normal development
Filament::addContext(string $name)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed
recently.

## Contributing

Please see
[CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for
details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report
security vulnerabilities.

## Credits

- [iotron](https://github.com/iotronlab)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more
information.
