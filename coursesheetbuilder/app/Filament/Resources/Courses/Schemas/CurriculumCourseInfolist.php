<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

/**
 * A tantárgy részletes nézethez használt Filament Infolist sémája.
 *
 * Megjeleníti a CurriculumCourse összes lényeges mezőjét (név három
 * nyelven, kód, kredit, órák, vizsgafórmátum, stb.) a ViewRecord oldalon.
 * A configure() statikus metódus konfigürálja a Schema-t.
 */
class CurriculumCourseInfolist
{
    /**
     * Definiálja az infolist megjelenítő mezőit.
     * Minden mező a CurriculumCourse modell attribútumait tüközi.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('curriculum.academicYear.year_code')
                    ->label('Tanév / Academic Year')
                    ->badge()
                    ->color('info'),
                TextEntry::make('course_name_hu')
                    ->label('Course Name (HU)'),
                TextEntry::make('course_name_en')
                    ->label('Course Name (EN)'),
                TextEntry::make('course_name_ro')
                    ->label('Course Name (RO)'),
                TextEntry::make('course_code')
                    ->label('Course Code'),
                TextEntry::make('credits')
                    ->label('Credits'),
                TextEntry::make('study_year')
                    ->label('Study Year'),
                TextEntry::make('semester')
                    ->label('Semester'),
                TextEntry::make('lecture_hours')
                    ->label('Lecture Hours'),
                TextEntry::make('seminar_hours')
                    ->label('Seminar Hours'),
                TextEntry::make('lab_hours')
                    ->label('Lab Hours'),
                TextEntry::make('project_hours')
                    ->label('Project Hours'),
                TextEntry::make('course_type')
                    ->label('Course Type'),
                TextEntry::make('formative_category')
                    ->label('Formative Category'),
                TextEntry::make('exam_type')
                    ->label('Exam Type'),
            ]);
    }
}
