<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Filament\Resources\JobResource\RelationManagers;
use App\Models\Job;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 90;

    public static function table(Table $table): Table
    {
        $commandNames = Job::all()->groupBy(function (Job $job) {
            return $job->getCommandNameAttribute();
        })->keys()->toArray();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make(Job::payload . '.' . Job::PAYLOAD_DATA . '.' . Job::PAYLOAD_DATA_COMMAND_NAME)
                    ->searchable(true, function ($query, string $search) {
                        $query->where(Job::payload, 'LIKE', "%{$search}%");
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make(Job::queue)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make(Job::attempts)
                    ->sortable(),
                Tables\Columns\TextColumn::make(Job::available_at)
                    ->sortable(),
                Tables\Columns\TextColumn::make(Job::created_at)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make(Job::queue)
                    ->label('Queue')
                    ->options(
                        Job::query()
                            ->get()
                            ->pluck(Job::queue, Job::queue)
                            ->unique()
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make(Job::payload . '.' . Job::PAYLOAD_DATA . '.' . Job::PAYLOAD_DATA_COMMAND_NAME)
                    ->label('Command')
                    ->options(array_combine($commandNames, $commandNames))
                    ->query(function (Builder $query, Tables\Filters\SelectFilter $filter) {
                        if ($value = $filter->getState()['value']) {
                            $query->scopes([
                                Job::SCOPE_JOB => $value
                            ]);
                        }
                    }),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
        ];
    }
}
