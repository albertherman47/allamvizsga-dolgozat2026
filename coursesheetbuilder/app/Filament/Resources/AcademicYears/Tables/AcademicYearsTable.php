<?php

namespace App\Filament\Resources\AcademicYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Tanévek listázó táblázatának Filament sémája.
 *
 * Megjeleníti az összes tanévet (évkód, kezdő év, befejező év, dátumok).
 * Tömeges törlés, megtekintés és szerkesztés műveletek elérhetők.
 */
class AcademicYearsTable
{
    /**
     * Konfigürálja a tanév-listázó táblázatot.
     * Oszlopok: year_code, start_year, end_year, created_at, updated_at.
     * Akciók: View, Edit (soronként), tömeges törlés.
     *
     * @param Table $table A táblázat példánya.
     * @return Table A konfigurált táblázat.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year_code')
                    ->label('Year Code')
                    ->searchable(),
                TextColumn::make('start_year')
                    ->label('Start Year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_year')
                    ->label('End Year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
