<?php

namespace App\Filament\Widgets;

use App\Models\CourseAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->isTeacher();
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $teacherId = $user->teacher?->id;

        if (!$teacherId) {
            return [];
        }

        // Count assignments where this teacher is involved in any capacity
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
