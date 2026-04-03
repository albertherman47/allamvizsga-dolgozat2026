<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name'),
                TextEntry::make('email')
                    ->label('Email'),
                TextEntry::make('roles.name')
                    ->label('Roles')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'super_admin' => 'Super Admin',
                        'department_admin' => 'Department Admin',
                        'teacher' => 'Teacher',
                        default => ucfirst(str_replace('_', ' ', $state ?? ''))
                    }),
                TextEntry::make('email_verified_at')
                    ->label('Email Verified At')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
