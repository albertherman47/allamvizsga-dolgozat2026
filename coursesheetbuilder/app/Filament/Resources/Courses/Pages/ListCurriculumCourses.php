<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CurriculumCourseResource;
use Filament\Resources\Pages\ListRecords;

/**
 * A tantárgyak listázó oldala (ListRecords) az oktatók számára.
 *
 * Megjeleníti az összes, az adott oktatóhoz rendelt tantárgyat
 * (ténylegesen a getEloquentQuery() szűrője határozza meg a kört).
 * Nem tartalmaz új rekord létrehozására szolgáló gombot, mivel az oktató
 * nem adhat hozzá új tantárgyat, csak adatlapot készíthet a meglévőkhöz.
 */
class ListCurriculumCourses extends ListRecords
{
    protected static string $resource = CurriculumCourseResource::class;

    /**
     * Fejlécakciók: szándékosan üres — oktató nem hozhat létre új tantárgyat.
     */
    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
