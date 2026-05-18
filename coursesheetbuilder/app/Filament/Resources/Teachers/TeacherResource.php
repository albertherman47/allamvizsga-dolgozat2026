<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Filament\Resources\Teachers\Pages\EditTeacher;
use App\Filament\Resources\Teachers\Pages\ListTeachers;
use App\Filament\Resources\Teachers\Pages\ViewTeacher;
use App\Filament\Resources\Teachers\Schemas\TeacherForm;
use App\Filament\Resources\Teachers\Schemas\TeacherInfolist;
use App\Filament\Resources\Teachers\Tables\TeachersTable;
use App\Models\Teacher;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource az oktatók (Teacher) kezeléséhez.
 *
 * Az oktató egy User-hez kapcsolódó profil, amely tartalmazza az akadémiai
 * fokozatot, beosztást, tanszéki hovatartozást és elérhetőségeket.
 * Csak a super_admin szerepkör látja a navigációban.
 *
 * Kapcsolódó modellek: Teacher, User, Department
 * Navigációs csoport: Users & Access | Ikon: user-group
 */
class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Tanárok';

    protected static \UnitEnum|string|null $navigationGroup = 'Tanárok';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'last_name';

    public static function form(Schema $schema): Schema
    {
        return TeacherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeacherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeachersTable::configure($table);
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
            'index' => ListTeachers::route('/'),
            'create' => CreateTeacher::route('/create'),
            'view' => ViewTeacher::route('/{record}'),
            'edit' => EditTeacher::route('/{record}/edit'),
        ];
    }

    /**
     * Csak super_admin szerepkörű felhasználók számára látható a navigációban.
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Show only for super_admin
        return $user && $user->hasRole('super_admin');
    }
}
