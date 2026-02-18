<?php

namespace App\Settings;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Spatie\LaravelSettings\Settings;

class DashboardSettings extends Settings implements FormSchemaInterface
{
    public bool $show_quick_insights;
    public bool $show_stats_overview;
    public bool $show_series_chart;
    public bool $show_episodes_by_season;
    public bool $show_recent_jobs;
    public bool $show_api_logs;
    public bool $show_api_stats_chart;
    public bool $show_top_series;

    public static function group(): string
    {
        return 'dashboard';
    }

    public static function getFormSchema(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Toggle::make('show_quick_insights')
                        ->label('Quick Insights Widget')
                        ->helperText('Zeigt schnelle Einblicke und wichtige Metriken auf einen Blick.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_stats_overview')
                        ->label('Statistik-Übersicht Widget')
                        ->helperText('Zeigt Statistiken zu Serien, Episoden, Jobs und API-Aufrufen.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_series_chart')
                        ->label('Serien-Diagramm Widget')
                        ->helperText('Balkendiagramm der Top 10 Serien mit Episoden-Vergleich.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_episodes_by_season')
                        ->label('Episoden nach Staffel Widget')
                        ->helperText('Liniendiagramm der Episoden-Verteilung nach Staffeln.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_recent_jobs')
                        ->label('Jobs Widget')
                        ->helperText('Tabelle der letzten Jobs in der Warteschlange.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_api_logs')
                        ->label('API-Logs Widget')
                        ->helperText('Tabelle der letzten TheTVDB API-Aufrufe mit Details.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_api_stats_chart')
                        ->label('API-Statistik Widget')
                        ->helperText('Liniendiagramm der API-Aufrufe der letzten 7 Tage.')
                        ->inline(false)
                        ->default(true),

                    Toggle::make('show_top_series')
                        ->label('Top Serien Widget')
                        ->helperText('Grid-Ansicht der Top 6 Serien mit Fortschrittsanzeige.')
                        ->inline(false)
                        ->default(true),
                ]),
        ];
    }
}

