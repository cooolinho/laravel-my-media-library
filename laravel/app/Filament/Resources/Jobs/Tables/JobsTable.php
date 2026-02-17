<?php

namespace App\Filament\Resources\Jobs\Tables;

use App\Models\Job;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        $commandNames = Job::all()->groupBy(function (Job $job) {
            return $job->getCommandNameAttribute();
        })->keys()->toArray();

        return $table
            ->columns([
                TextColumn::make(Job::payload . '.' . Job::PAYLOAD_DATA . '.' . Job::PAYLOAD_DATA_COMMAND_NAME)
                    ->searchable(true, function ($query, string $search) {
                        $query->where(Job::payload, 'LIKE', "%{$search}%");
                    })
                    ->sortable(),
                TextColumn::make(Job::queue)
                    ->searchable()
                    ->sortable(),
                TextColumn::make(Job::attempts)
                    ->sortable(),
                TextColumn::make(Job::available_at)
                    ->sortable(),
                TextColumn::make(Job::created_at)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make(Job::queue)
                    ->label('Queue')
                    ->options(
                        Job::query()
                            ->get()
                            ->pluck(Job::queue, Job::queue)
                            ->unique()
                            ->toArray()
                    ),
                SelectFilter::make(Job::payload . '.' . Job::PAYLOAD_DATA . '.' . Job::PAYLOAD_DATA_COMMAND_NAME)
                    ->label('Command')
                    ->options(array_combine($commandNames, $commandNames))
                    ->query(function (Builder $query, SelectFilter $filter) {
                        if ($value = $filter->getState()['value']) {
                            $query->scopes([
                                Job::SCOPE_JOB => $value
                            ]);
                        }
                    }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
