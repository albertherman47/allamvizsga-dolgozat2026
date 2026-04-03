<?php

namespace App\Filament\Resources\Departments\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_hu')
                    ->required(),
                TextInput::make('name_ro')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                TextInput::make('head_name')
                    ->default(null),
            ]);
    }
}
