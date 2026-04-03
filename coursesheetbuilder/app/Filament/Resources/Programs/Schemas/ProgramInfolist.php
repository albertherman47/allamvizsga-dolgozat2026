<?php

namespace App\Filament\Resources\Programs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('department.name_hu')
                    ->label('Department'),
                TextEntry::make('code'),
                TextEntry::make('name_hu'),
                TextEntry::make('name_ro'),
                TextEntry::make('name_en'),
                TextEntry::make('domain')
                    ->placeholder('-'),
                TextEntry::make('cycle')
                    ->badge(),
                TextEntry::make('qualification')
                    ->placeholder('-'),
                TextEntry::make('coordinator.full_name')
                    ->label('Coordinator')
                    ->placeholder('-'),
                TextEntry::make('programManager.full_name')
                    ->label('Program manager')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
