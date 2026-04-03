<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('department_id')
                    ->relationship('department', 'name_hu')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('name_hu')
                    ->required(),
                TextInput::make('name_ro')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                TextInput::make('domain')
                    ->default(null),
                Select::make('cycle')
                    ->options(['Licență' => 'Licență', 'Master' => 'Master', 'Doctorat' => 'Doctorat'])
                    ->required(),
                TextInput::make('qualification')
                    ->default(null),
                Select::make('coordinator_id')
                    ->relationship('coordinator', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->default(null),
                Select::make('program_manager_id')
                    ->relationship('programManager', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->preload()
                    ->default(null),
                TextInput::make('duration_years')
                    ->label('Duration (Years)')
                    ->numeric()
                    ->default(3),
                TextInput::make('total_semesters')
                    ->label('Total Semesters')
                    ->numeric()
                    ->default(6),
            ]);
    }
}
