<?php

namespace Iotronlab\FilamentMultiGuard;

use Filament\Facades\Filament;
use Filament\FilamentManager;
use Illuminate\Support\Traits\ForwardsCalls;

class FilamentMultiGuard
{

    use ForwardsCalls;

    protected array $contexts = [];

    protected ?string $currentContext = null;

    /**
     * @param FilamentManager $filament
     */
    public function __construct(FilamentManager $filament)
    {
        $this->contexts['filament'] = $filament;
    }

    /**
     * @param string|null $context
     * @return $this
     */
    public function setContext(string $context = null)
    {
        $this->currentContext = $context;

        return $this;
    }

    /**
     * @return string
     */
    public function currentContext(): string
    {
        return $this->currentContext ?? 'filament';
    }

    public function getContext()
    {
        return $this->contexts[$this->currentContext ?? 'filament'];
    }

    public function getContexts(): array
    {
        return $this->contexts;
    }

    public function addContext(string $name)
    {
        $context = new ContextManager();
        $context::$config = $name;
        $this->contexts[$name] = $context;
        return $this;
    }

    public function forContext(string $context, callable $callback)
    {
        $currentContext = Filament::currentContext();

        Filament::setContext($context);

        $callback();

        Filament::setContext($currentContext);

        return $this;
    }

    public function forAllContexts(callable $callback)
    {
        $currentContext = Filament::currentContext();

        foreach ($this->contexts as $key => $context) {
            Filament::setContext($key);

            $callback();
        }

        Filament::setContext($currentContext);

        return $this;
    }

    /**
     * Dynamically handle calls into the filament instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $response = $this->forwardCallTo($this->getContext(), $method, $parameters);

        if ($response instanceof FilamentManager) {
            return $this;
        }

        return $response;
    }


}
