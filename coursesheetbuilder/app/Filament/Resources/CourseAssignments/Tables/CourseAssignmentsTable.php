<?php

namespace App\Filament\Resources\CourseAssignments\Tables;

use App\Models\Program;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CourseAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('curriculum.program.name_hu')
                    ->label('Szak')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('course_code')
                    ->label('Kód')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_name_hu')
                    ->label('Kurzus neve')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('semester')
                    ->label('Félév')
                    ->sortable(),
                TextColumn::make('course_leader')
                    ->label('Előadó')
                    ->state(function ($record) {
                        return $record->courseAssignments->first()?->courseLeader?->full_name ?? '-';
                    }),
            ])
            ->filters([
                SelectFilter::make('program_id')
                    ->label('Szak (Program)')
                    ->options(Program::pluck('name_hu', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            $query->whereHas('curriculum', function ($q) use ($data) {
                                $q->where('program_id', $data['value']);
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('Tanárok hozzárendelése')
                    ->icon('heroicon-m-users'),
            ]);
    }
}
