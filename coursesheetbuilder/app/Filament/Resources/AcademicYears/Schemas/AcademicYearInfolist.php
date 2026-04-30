<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AcademicYearInfolist
{
    /**
     * Configures the schema with specified components.
     *
     * @param Schema $schema The schema instance to be configured.
     * @return Schema The configured schema instance.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('year_code'),
                TextEntry::make('start_year')
                    ->numeric(),
                TextEntry::make('end_year')
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
