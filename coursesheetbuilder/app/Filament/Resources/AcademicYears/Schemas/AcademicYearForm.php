<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AcademicYearForm
{
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
