<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Syllabi\CourseSyllabusContentResource;
use App\Models\CourseSyllabusContent;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Dashboard widget: az utolsó 5 szerkesztett tantárgy-adatlapot jeleníti meg.
 *
 * Teljes szélességű táblázat, amely a legutolsó frissítési idő szerint
 * rendezve mutatja az adatlapokat. Teacher esetén csak a hozzá rendelt
 * adatlapokat látja; admin minden adatlapot lát.
 * A tantárgy nevére kattintva egyből a szerkesztőre visz.
 */
class LatestSyllabiWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

   
    /**
     * A widget táblázatát konfigürálja.
     * Szűrés: teacher esetén csak a hozzá rendelt CourseSyllabusContent rekordok.
     * Oszlopok: tantárgy neve (kattintható link), állapot badge, frissítési idő.
     */
    public function table(Table $table): Table
    {
        $user = Auth::user();
        $query = CourseSyllabusContent::query()->with(['courseAssignment.curriculumCourse'])->latest('updated_at')->limit(5);
        
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
        
        return $table
            ->query($query)
            ->heading('📋 Legutóbb Szerkesztett Adatlapok')
            ->description('Közvetlen hozzáférés az éppen kidolgozás alatt álló dokumentumokhoz.')
            ->columns([
                Tables\Columns\TextColumn::make('courseAssignment.curriculumCourse.course_name_ro')
                    ->label('Disciplina (Tantárgy)')
                    ->description(fn (CourseSyllabusContent $record): string => $record->courseAssignment->curriculumCourse->course_code ?? 'N/A')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-academic-cap')
                    // A névre kattintva egyből szerkesztő oldalra visz
                    ->url(fn (CourseSyllabusContent $record): string => CourseSyllabusContentResource::getUrl('edit', ['record' => $record]))
                    ->color('primary')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Stare Fișă')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'draft' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? 'Draft')),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Utoljára Frissítve')
                    ->since()
                    ->sortable()
                    ->icon('heroicon-o-clock'),
            ])
            ->paginated(false);
    }
}
