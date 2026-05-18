<?php

namespace App\Filament\Resources\Curricula\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CurriculumInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('program.id')
                    ->label('Program'),
                TextEntry::make('academicYear.id')
                    ->label('Academic year'),
                TextEntry::make('name'),
                TextEntry::make('hours_per_credit')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
