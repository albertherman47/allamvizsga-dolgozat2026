<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Filament adminfelület-resource a rendszer-felhasználók (User) kezeléséhez.
 *
 * Kezeli az összes felhasználói fiókot (név, email, jelszó, szerepkör).
 * Csak a super_admin szerepkör érheti el — a tanárok és staff saját
 * profiljukat külön felületen módosíthatják.
 *
 * Kapcsolódó modellek: User
 * Navigációs csoport: Users & Access | Ikon: user
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Felhasználók';

    protected static \UnitEnum|string|null $navigationGroup = 'Felhasználók';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
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
