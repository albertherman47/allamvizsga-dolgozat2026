<?php

namespace App\Filament\Resources\CourseAssignments\Pages;

use App\Filament\Resources\CourseAssignments\CourseAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseAssignments extends ListRecords
{
    protected static string $resource = CourseAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
