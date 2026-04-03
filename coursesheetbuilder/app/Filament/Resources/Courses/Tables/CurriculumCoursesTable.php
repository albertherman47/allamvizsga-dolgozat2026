<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculumCoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_code')
                    ->label('Course Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_name_hu')
                    ->label('Course Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('study_year')
                    ->label('Year')
                    ->sortable(),
                TextColumn::make('semester')
                    ->label('Semester')
                    ->sortable(),
                TextColumn::make('credits')
                    ->label('Credits')
                    ->sortable(),
                TextColumn::make('lecture_hours')
                    ->label('Lectures')
                    ->sortable(),
                TextColumn::make('seminar_hours')
                    ->label('Seminars')
                    ->sortable(),
                TextColumn::make('lab_hours')
                    ->label('Labs')
                    ->sortable(),
                TextColumn::make('course_type')
                    ->label('Type')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
