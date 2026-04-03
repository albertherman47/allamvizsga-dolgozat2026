<?php

namespace App\Filament\Widgets;

use App\Models\CurriculumCourse;
use App\Models\Program;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->hasRole('admin') || (!$user->isTeacher() && !$user->isAdministrativeStaff());
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Oktatók száma', Teacher::count())
                ->description('Rendszerben lévő oktatók')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Szakok (Programs) száma', Program::count())
                ->description('Aktív szakok')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
            Stat::make('Tantárgyak Száma', CurriculumCourse::count())
                ->description('Minden rögzített tananyag')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
        ];
    }
}
