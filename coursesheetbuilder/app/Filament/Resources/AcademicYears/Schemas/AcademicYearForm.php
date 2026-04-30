<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Tanév létrehozás/szerkesztés űrlap sémája.
 *
 * Mezők: year_code (pl. "2025/26"), start_year, end_year, hours_per_credit.
 * A hours_per_credit alapértelmezése 28 óra/kredit.
 */
class AcademicYearForm
{
    /**
     * Konfigürálja a tanév-űrlap sémáját (létrehozás és szerkesztés).
     *
     * @param Schema $schema Az űrlap sémája.
     * @return Schema A konfigurált séma.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year_code')
                    ->label('Year Code')
                    ->required(),
                TextInput::make('start_year')
                    ->label('Start Year')
                    ->required()
                    ->numeric(),
                TextInput::make('end_year')
                    ->label('End Year')
                    ->required()
                    ->numeric(),
                TextInput::make('hours_per_credit')
                    ->label('Hours Per Credit')
                    ->numeric()
                    ->default(28),
            ]);
    }
}
