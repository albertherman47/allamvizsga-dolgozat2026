<?php

namespace App\Filament\Widgets;

use App\Models\CourseSyllabusContent;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Tantárgy-adatlap statisztikai widget az oktatói dashboardon.
 *
 * Kizárólag a "teacher" szerepkörű felhasználóknak jelenik meg (az admin
 * nem látja). Három számlálót mutat az oktató saját adatlapjaira
 * vonatkozóan: összes adatlap, piszkozatok (draft), véglegesített (completed).
 */
class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Meghatározza, hogy ez a widget látható-e az adott felhasználó számára.
     * Csak "teacher" szerepkörű felhasználóknak jelenik meg, adminnak nem.
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        // Csak tanár (aki dolgozik velük) láthatja, adminnak ez nem jelenik meg!
        return $user && method_exists($user, 'hasRole') && $user->hasRole('teacher') && !$user->hasRole('admin');
    }

    /**
     * Az oktató saját adatlapjaira vonatkozó statisztikákat adja vissza.
     * Ha teacher a felhasználó, csak a hozzá rendelt adatlapokat számolja.
     * Visszaadja: összes, piszkozat, véglegesített adatlapok számát.
     */
    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Ha teacher, csak a sajátjait lássa
        $query = CourseSyllabusContent::query();
        
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('teacher')) {
            $teacherId = $user->teacher->id ?? null;
            if ($teacherId) {
                $query->whereHas('courseAssignment', function ($q) use ($teacherId) {
                    $q->where('course_leader_id', $teacherId)
                      ->orWhere('seminar_leader_id', $teacherId)
                      ->orWhere('lab_leader_id', $teacherId)
                      ->orWhere('project_leader_id', $teacherId);
                });
            }
        }
        
        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        
        // draft or null
        $draft = clone $query;
        $draft->where(function ($q) {
            $q->where('status', 'draft')->orWhereNull('status');
        });
        $draftCount = $draft->count();
        
        return [
            Stat::make('Adatlapok Összesen', $total)
                ->description('Ajtódhoz rendelt fișe-k száma')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Piszkozatok', $draftCount)
                ->description('Jelenleg szerkesztés alatt')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning')
                ->chart([3, 5, 2, 7, 5, 2, 1]),
                
            Stat::make('Véglegesített', $completed)
                ->description('Jóváhagyásra váró / Lezárt')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 2, 5, 3, 8, 12, 15]),
        ];
    }
}
