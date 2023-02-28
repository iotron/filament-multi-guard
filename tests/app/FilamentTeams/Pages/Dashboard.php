<?php

namespace Iotronlab\FilamentMultiGuard\Tests\app\FilamentTeams\Pages;
use Filament\Pages\Dashboard as FilamentCoreDashboard;
use Iotronlab\FilamentMultiGuard\Concerns\ContextualPage;

class Dashboard extends FilamentCoreDashboard
{
    use ContextualPage;
}
