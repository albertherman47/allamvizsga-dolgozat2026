<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\Departments\Pages\EditDepartment;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Filament\Resources\Departments\Pages\ViewDepartment;
use App\Filament\Resources\Departments\Schemas\DepartmentForm;
use App\Filament\Resources\Departments\Schemas\DepartmentInfolist;
use App\Filament\Resources\Departments\Tables\DepartmentsTable;
use App\Models\Department;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource a tanszékek (Department) kezeléséhez.
 *
 * Biztosítja a tanszékek létrehozását, szerkesztését, megtekintését és
 * listázását. Csak adminisztratív szerepkörű felhasználók (super_admin,
 * department_admin) érhetik el a navigációban.
 *
 * Kapcsolódó modellek: Department
 * Navigációs csoport: Department & Organization | Ikon: building-office
 */
class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Tanszékek';

    protected static \UnitEnum|string|null $navigationGroup = 'Intézmény';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name_hu';

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
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
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view' => ViewDepartment::route('/{record}'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }

    /**
     * Csak super_admin és department_admin szerepkörű felhasználók látják a navigációban.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Show for super_admin and department_admin
        return $user && ($user->hasRole('super_admin') || $user->hasRole('department_admin'));
    }
}
