<?php

namespace App\Filament\Resources\CourseAssignments;

use App\Filament\Resources\CourseAssignments\Pages\CreateCourseAssignment;
use App\Filament\Resources\CourseAssignments\Pages\EditCourseAssignment;
use App\Filament\Resources\CourseAssignments\Pages\ListCourseAssignments;
use App\Filament\Resources\CourseAssignments\Schemas\CourseAssignmentForm;
use App\Filament\Resources\CourseAssignments\Tables\CourseAssignmentsTable;
use App\Models\CurriculumCourse;
use App\Models\CourseAssignment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource az oktató-tantárgy hozzárendelések kezeléséhez.
 *
 * Megjeleníti, hogy melyik tantárgyhoz (CurriculumCourse) melyik oktatók
 * vannak hozzárendelve (előadás, szemérinárium, labor, projekt szerepek szerint).
 * A modell technikailag a CurriculumCourse, de az átempítés és a form a
 * CourseAssignment rekordokat kezeli.
 *
 * Navigációs láthatóság: super_admin és department_admin szerepkörök.
 * Elérhető műveletek: listázás, szerkesztés.
 */
class CourseAssignmentResource extends Resource
{
    protected static ?string $model = CurriculumCourse::class;

    protected static ?string $navigationLabel = 'Course Assignments';
    protected static ?string $modelLabel = 'Course Assignment';
    protected static ?string $pluralModelLabel = 'Course Assignments';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return CourseAssignmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseAssignmentsTable::configure($table);
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
            'index' => ListCourseAssignments::route('/'),
            'edit' => EditCourseAssignment::route('/{record}/edit'),
        ];
    }

    /**
     * Csak super_admin és department_admin szerepkörű felhasználók látják a menüben.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('department_admin'));
    }
}
