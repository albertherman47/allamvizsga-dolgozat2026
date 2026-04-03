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

class CourseAssignmentResource extends Resource
{
    protected static ?string $model = CurriculumCourse::class;

    protected static ?string $navigationLabel = 'Tantárgyfelosztás';
    protected static ?string $modelLabel = 'Tantárgyfelosztás';
    protected static ?string $pluralModelLabel = 'Tantárgyfelosztások';

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

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('department_admin'));
    }
}
