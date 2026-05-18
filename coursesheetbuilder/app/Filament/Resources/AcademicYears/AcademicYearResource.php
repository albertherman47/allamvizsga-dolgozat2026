<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\AcademicYears\Pages\CreateAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\EditAcademicYear;
use App\Filament\Resources\AcademicYears\Pages\ListAcademicYears;
use App\Filament\Resources\AcademicYears\Pages\ViewAcademicYear;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearForm;
use App\Filament\Resources\AcademicYears\Schemas\AcademicYearInfolist;
use App\Filament\Resources\AcademicYears\Tables\AcademicYearsTable;
use App\Models\AcademicYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource a tanévek (AcademicYear) kezeléséhez.
 *
 * Csak a "super_admin" szerepkörű felhasználók látják a navigációban
 * (Settings csoport). Létrehozás, megtekintés, szerkesztés, listázás
 * műveletek elérhetők (CRUD teljes köre).
 *
 * Kapcsolódó modellek: AcademicYear
 * Navigációs csoport: Settings | Ikon: calendar-days
 */
class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Tanévek';

    protected static \UnitEnum|string|null $navigationGroup = 'Intézmény';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'year_code';

    public static function form(Schema $schema): Schema
    {
        return AcademicYearForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AcademicYearInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AcademicYearsTable::configure($table);
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
            'index' => ListAcademicYears::route('/'),
            'create' => CreateAcademicYear::route('/create'),
            'view' => ViewAcademicYear::route('/{record}'),
            'edit' => EditAcademicYear::route('/{record}/edit'),
        ];
    }

    /**
     * Meghatározza, hogy a navigációban megjelenjen-e ez a resource.
     * Csak a super_admin szerepkörű felhasználók számára látható.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Show only for super_admin
        return $user && $user->hasRole('super_admin');
    }
}
