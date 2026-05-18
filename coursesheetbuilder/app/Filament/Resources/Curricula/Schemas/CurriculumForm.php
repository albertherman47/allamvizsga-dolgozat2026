<?php

namespace App\Filament\Resources\Curricula\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CurriculumForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('program_id')
                    ->relationship('program', 'name_hu')
                    ->label('Szak')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('academic_year_id')
                    ->relationship('academicYear', 'year_code')
                    ->label('Tanév')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Tanterv neve')
                    ->placeholder('Pl.: Informatika 2024/25')
                    ->required(),
                TextInput::make('hours_per_credit')
                    ->label('Óra / Kredit')
                    ->required()
                    ->numeric()
                    ->default(28),
            ]);
    }
}
