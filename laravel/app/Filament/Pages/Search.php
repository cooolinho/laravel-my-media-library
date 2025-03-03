<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Search extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Suche'; // Name im Menü
    protected static ?int $navigationSort = 2; // Sortierung im Menü

    protected static string $view = 'filament.pages.search';
}
