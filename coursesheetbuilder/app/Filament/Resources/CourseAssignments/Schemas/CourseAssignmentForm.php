<?php

namespace App\Filament\Resources\CourseAssignments\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

/**
 * Oktató-tantárgy hozzárendelés szerkesztő űrlap sémája.
 *
 * Egy Repeater mezoőt tartalmaz (max 1 elem), amelyen belül a négy
 * oktatói szerepet lehet kiosztani: előadó, szemináriumvezető,
 * laborvezető és projektvezető. Az oktatókat név szerint lehet keresni.
 */
class CourseAssignmentForm
{
    /**
     * Konfigürálja a tantárgy-hozzárendelési űrlap sémáját.
     * Egy Repeater-en belül négy Select mezőt kínál az oktatói szerepek kitöltéséhez.
     *
     * @param Schema $schema Az űrlap sémája.
     * @return Schema A konfigurált séma.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('courseAssignments')
                    ->relationship('courseAssignments')
                    ->label('Hozzárendelt tanárok')
                    ->schema([
                        Select::make('course_leader_id')
                            ->relationship('courseLeader', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->label('Előadó (Course Leader)')
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable(),
                        Select::make('seminar_leader_id')
                            ->relationship('seminarLeader', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->label('Szemináriumvezető')
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable(),
                        Select::make('lab_leader_id')
                            ->relationship('labLeader', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->label('Laborvezető')
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable(),
                        Select::make('project_leader_id')
                            ->relationship('projectLeader', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->label('Projektvezető')
                            ->searchable(['first_name', 'last_name'])
                            ->preload()
                            ->nullable(),
                    ])
                    ->maxItems(1)
                    ->defaultItems(1)
                    ->columnSpanFull()
            ]);
    }
}
