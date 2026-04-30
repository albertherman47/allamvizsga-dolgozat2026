<?php

namespace App\Filament\Resources\AcademicYears\Pages;

use App\Filament\Resources\AcademicYears\AcademicYearResource;
use Filament\Resources\Pages\CreateRecord;

/** Új tanév létrehozásának Filament oldala. */
class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;
}
