<?php

namespace App\Filament\Widgets;

use App\Models\CourseAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Oktatói statisztikai widget a dashboardon.
 *
 * Kizárólag oktatói szerepkörű felhasználóknak jelenik meg (isTeacher()).
 * Egyetlen számlálót mutat: hány tantárgyhoz van az oktató hozzárendelve
 * (előadó, szemináriumvezető, laborvezető és projektfelelős szerepekben összesítve).
 */
class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Csak oktató (Teacher) szerepkörű felhasználóknak jelenik meg.
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user->isTeacher();
    }

    /**
     * Az oktatóhoz rendelt tantárgyak számát adja vissza.
     * Összeadja az előadó, szemináriumvezető, laborvezető és
     * projektfelelős szerepkiosztott tantárgyainak számát.
     */
    protected function getStats(): array
    {
        
        $user = auth()->user();

        $teacherId = $user->teacher?->id;

        if (!$teacherId) {
            return [];
        }

        
        $assignmentsCount = collect([
            CourseAssignment::where('course_leader_id', $teacherId)->count(),
            CourseAssignment::where('seminar_leader_id', $teacherId)->count(),
            CourseAssignment::where('lab_leader_id', $teacherId)->count(),
            CourseAssignment::where('project_leader_id', $teacherId)->count(),
        ])->sum();

        return [
            Stat::make('Saját Tantárgyaim', $assignmentsCount)
                ->description('Tantárgyak, melyekhez oktatóként vagyok rendelve')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
        ];
    }
}
