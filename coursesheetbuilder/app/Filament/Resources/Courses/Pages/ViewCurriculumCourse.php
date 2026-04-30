<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CurriculumCourseResource;
use App\Models\CourseAssignment;
use App\Models\CourseSyllabusContent;
use App\Models\SyllabusTemplate;
use App\Services\CourseSyllabusFormBuilder;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

/**
 * Egy tantárgy részletes megtekintésének Filament oldalas (ViewRecord).
 *
 * A fejlécben egy "Create Syllabus" gomb jelenik meg, amely:
 * 1. Megkeresi a tantárgyhoz tartozó CourseAssignment rekordokat.
 * 2. Ellenőrzi, hogy létezik-e aktív SyllabusTemplate az adott tanévhez.
 * 3. Minden hozzárendeléshez létrehoz egy CourseSyllabusContent rekordot
 *    (ha még nem létezik), majd átirányít a szerkesztőre.
 */
class ViewCurriculumCourse extends ViewRecord
{
    protected static string $resource = CurriculumCourseResource::class;

    /**
     * A fejlécben megjelenő akciógombokat adja vissza.
     * Tartalmazza: Create Syllabus akció + alapértelmezett Edit akció.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_syllabus')
                ->label('Create Syllabus')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->action(function () {
                    // Get course assignments for this curriculum course
                    $assignments = CourseAssignment::where('curriculum_course_id', $this->record->id)->get();

                    if ($assignments->isEmpty()) {
                        Notification::make()
                            ->warning()
                            ->title('No course assignments found')
                            ->body('No course assignments found for this course.')
                            ->send();
                        return;
                    }

                    // Get the active template for the academic year
                    $academicYearId = $this->record->curriculum->academic_year_id;
                    $template = SyllabusTemplate::where('academic_year_id', $academicYearId)
                        ->where('is_active', true)
                        ->first();

                    if (!$template) {
                        Notification::make()
                            ->danger()
                            ->title('No template found')
                            ->body('No active syllabus template found for this academic year.')
                            ->send();
                        return;
                    }

                    // Create syllabus for each assignment that doesn't have one
                    $created = 0;
                    $builder = new CourseSyllabusFormBuilder();

                    foreach ($assignments as $assignment) {
                        // Check if syllabus already exists
                        $existing = CourseSyllabusContent::where('course_assignment_id', $assignment->id)->first();
                        if (!$existing) {
                            // Get initial form data with structure
                            $initialData = $builder->getInitialFormData($assignment);

                            CourseSyllabusContent::create([
                                'course_assignment_id' => $assignment->id,
                                'template_id' => $template->id,
                                'editable_data' => $initialData['editable_data'],
                                'status' => 'draft',
                            ]);
                            $created++;
                        }
                    }

                    if ($created > 0) {
                        Notification::make()
                            ->success()
                            ->title('Syllabus created')
                            ->body("Created $created syllabus record(s). Redirecting to edit...")
                            ->send();
                        // Redirect to edit the first created syllabus
                        $syllabusContent = CourseSyllabusContent::where('course_assignment_id', $assignments->first()->id)->first();
                        return redirect()->route('filament.admin.resources.syllabi.course-syllabus-contents.edit', $syllabusContent);
                    } else {
                        Notification::make()
                            ->info()
                            ->title('Syllabus exists')
                            ->body('Syllabus already exists for all assignments.')
                            ->send();
                    }
                }),
            Actions\EditAction::make(),
        ];
    }
}
