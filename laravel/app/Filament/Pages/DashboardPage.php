<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class DashboardPage extends Dashboard
{
    public function getPageClasses(): array
    {
        return [
            'fi-body-dashboard-page',
        ];
    }
}
