<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CurriculumCourseResource;
use Filament\Resources\Pages\ListRecords;

class ListCurriculumCourses extends ListRecords
{
    protected static string $resource = CurriculumCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
