<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CurriculumCourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
