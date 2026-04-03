<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('department_id')
                    ->relationship('department', 'name_hu')
                    ->required(),
                Select::make('academic_degree')
                    ->options(['dr.' => 'Dr.', 'drd.' => 'Drd.', 'dr. habil.' => 'Dr. habil.'])
                    ->default(null),
                Select::make('position')
                    ->options([
            'Prof. univ.' => 'Prof. univ.',
            'Conf. univ.' => 'Conf. univ.',
            'Lect. univ.' => 'Lect. univ.',
            'Asist. univ.' => 'Asist. univ.',
        ])
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('neptun_code')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('office_location')
                    ->default(null),
                Textarea::make('consultation_hours')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
