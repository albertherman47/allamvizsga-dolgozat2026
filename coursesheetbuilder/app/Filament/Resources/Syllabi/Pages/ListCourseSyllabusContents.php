<?php

namespace App\Filament\Resources\Syllabi\Pages;

use App\Filament\Resources\Syllabi\CourseSyllabusContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCourseSyllabusContents extends ListRecords
{
    protected static string $resource = CourseSyllabusContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
