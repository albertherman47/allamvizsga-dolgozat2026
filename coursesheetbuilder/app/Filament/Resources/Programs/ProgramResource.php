<?php

namespace App\Filament\Resources\Programs;

use App\Filament\Resources\Programs\Pages\CreateProgram;
use App\Filament\Resources\Programs\Pages\EditProgram;
use App\Filament\Resources\Programs\Pages\ListPrograms;
use App\Filament\Resources\Programs\Pages\ViewProgram;
use App\Filament\Resources\Programs\Schemas\ProgramForm;
use App\Filament\Resources\Programs\Schemas\ProgramInfolist;
use App\Filament\Resources\Programs\Tables\ProgramsTable;
use App\Models\Program;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource a szakok (Program) kezeléséhez.
 *
 * A szak a tanszékhez (Department) tartozó képzési forma, amelyhez
 * tantervek (Curriculum) kapcsolódnak. Tartalmazza a szak koordinátorát
 * és programfelelősét is. CRUD műveletek teljes köre elérhető.
 *
 * Kapcsolódó modellek: Program, Department, Teacher
 * Navigációs csoport: Department & Organization | Ikon: book-open
 */
class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Szakok';

    protected static \UnitEnum|string|null $navigationGroup = 'Intézmény';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name_hu';

    public static function form(Schema $schema): Schema
    {
        return ProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramsTable::configure($table);
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
            'index' => ListPrograms::route('/'),
            'create' => CreateProgram::route('/create'),
            'view' => ViewProgram::route('/{record}'),
            'edit' => EditProgram::route('/{record}/edit'),
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
