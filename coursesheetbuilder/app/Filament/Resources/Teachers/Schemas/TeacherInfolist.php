<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('department.id')
                    ->label('Department'),
                TextEntry::make('academic_degree')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('position')
                    ->badge(),
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('neptun_code'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('office_location')
                    ->placeholder('-'),
                TextEntry::make('consultation_hours')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
