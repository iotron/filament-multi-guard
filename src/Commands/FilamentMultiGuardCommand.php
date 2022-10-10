<?php

namespace Iotronlab\FilamentMultiGuard\Commands;

use Illuminate\Console\Command;

class FilamentMultiGuardCommand extends Command
{
    public $signature = 'filament-multi-guard';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
