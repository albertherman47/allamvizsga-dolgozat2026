<?php

namespace App\Filament\Resources\Courses;

use App\Filament\Resources\Courses\Pages\ListCurriculumCourses;
use App\Filament\Resources\Courses\Pages\ViewCurriculumCourse;
use App\Filament\Resources\Courses\Schemas\CurriculumCourseInfolist;
use App\Filament\Resources\Courses\Tables\CurriculumCoursesTable;
use App\Models\CourseAssignment;
use App\Models\CurriculumCourse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CurriculumCourseResource extends Resource
{
    protected static ?string $model = CurriculumCourse::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'My Courses';

    protected static \UnitEnum|string|null $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'course_name_hu';

    public static function infolist(Schema $schema): Schema
    {
        return CurriculumCourseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurriculumCoursesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurriculumCourses::route('/'),
            'view' => ViewCurriculumCourse::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Show only courses assigned to the current teacher
        if ($user && $user->hasRole('teacher')) {
            $teacher = $user->teacher;
            if ($teacher) {
                $assignedCourseIds = CourseAssignment::where(function ($query) use ($teacher) {
                    $query->where('course_leader_id', $teacher->id)
                        ->orWhere('seminar_leader_id', $teacher->id)
                        ->orWhere('lab_leader_id', $teacher->id)
                        ->orWhere('project_leader_id', $teacher->id);
                })
                    ->pluck('curriculum_course_id')
                    ->toArray();

                return $query->whereIn('id', $assignedCourseIds);
            }
        }

        // Department admin and super admin can see all courses
        return $query;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Show only for teachers
        return $user && $user->hasRole('teacher');
    }
}
