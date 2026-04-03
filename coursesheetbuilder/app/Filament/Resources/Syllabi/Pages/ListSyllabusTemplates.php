<?php

namespace App\Filament\Resources\Syllabi\Pages;

use App\Filament\Resources\Syllabi\SyllabusTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSyllabusTemplates extends ListRecords
{
    protected static string $resource = SyllabusTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
