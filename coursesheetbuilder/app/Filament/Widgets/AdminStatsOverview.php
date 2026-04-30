<?php

namespace App\Filament\Widgets;

use App\Models\CurriculumCourse;
use App\Models\Program;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Admin áttekintő widget a dashboard-on.
 *
 * Csak akkor jelenik meg, ha a bejelentkezett felhasználó admin vagy
 * sem oktató, sem adminisztratív munkatárs (azaz rendszer-adminisztratór).
 * Három statisztikát mutat: oktatók száma, szakok száma, tantárgyak száma.
 */
class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Meghatározza, hogy ez a widget látható-e az aktuális felhasználó számára.
     * Admin szerepkörnek vagy olyan felhasználóknak jelenik meg, akik nem oktatók
     * és nem adminisztratív munkatársak.
     */
    public static function canView(): bool
    {
        
        $user = auth()->user();
        return $user->hasRole('admin') || (!$user->isTeacher() && !$user->isAdministrativeStaff());
    }

    /**
     * Az adminnak megjelenitő statisztikai kártyákat adja vissza.
     * Tartalmazza: oktatók száma, aktiv szakok, összes tantárgy.
     */
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
