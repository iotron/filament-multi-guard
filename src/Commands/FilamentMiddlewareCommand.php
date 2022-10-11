<?php

namespace Iotronlab\FilamentMultiGuard\Commands;

use Illuminate\Console\Command;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;

class FilamentMiddlewareCommand extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    protected $signature = 'make:filament-middleware {name?} {--F|force}';

    protected $description = 'Create a Filament middleware for context';

    public function handle(): int
    {
        $context = Str::of($this->getContextInput())
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $this->copyStubs($context);

        return self::SUCCESS;
    }

    protected function copyStubs($context)
    {
        $middlewareClass = $context->afterLast('\\')->append('Middleware');

        $contextName = $context->afterLast('\\')->kebab();

        $middlewarePath = $middlewareClass
            ->prepend('/')
            ->prepend(app_path('Http/Middleware'))
            ->append('.php');

        if (!$this->option('force') && $this->checkForCollision([$middlewarePath])) {
            return static::INVALID;
        }

        $this->copyStubToApp('ContextMiddleware', $middlewarePath, [
            'class' => (string) $middlewareClass,
            'name' => (string) $contextName,
        ]);
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (!$this->fileExists($stubPath = base_path("stubs/filament/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../stubs/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
    }
}
