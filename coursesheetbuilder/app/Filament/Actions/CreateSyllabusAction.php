<?php

namespace App\Filament\Actions;

use App\Models\CourseAssignment;
use App\Models\SyllabusTemplate;
use App\Services\SyllabusService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class CreateSyllabusAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'create_syllabus';
    }

    public static function make(string $name = null): static
    {
        $action = parent::make($name ?? static::getDefaultName())
            ->label('Elkészítés')
            ->icon('heroicon-m-document-plus')
            ->color('success')
            ->modal()
            ->form([
                Select::make('course_assignment_id')
                    ->label('Tárgy')
                    ->options(fn () => self::getCourseOptions())
                    ->required()
                    ->searchable(),
            ])
            ->action(function (array $data) {
                $courseAssignment = CourseAssignment::findOrFail($data['course_assignment_id']);
                $template = SyllabusTemplate::where('is_active', true)
                    ->whereHas('academicYear')
                    ->first();

                if (!$template) {
                    throw new \Exception('No active syllabus template found.');
                }

                $syllabusService = new SyllabusService($template, $courseAssignment);
                $content = $syllabusService->initializeOrGetContent();

                // Redirect to edit page
                redirect(route('filament.admin.resources.syllabi.course-syllabus-contents.edit', $content->id));
            });

        return $action;
    }

    protected static function getCourseOptions(): array
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return [];
        }

        return CourseAssignment::where('course_leader_id', $teacher->id)
            ->whereDoesntHave('syllabusContent')
            ->with('curriculumCourse')
            ->get()
            ->mapWithKeys(fn ($assignment) => [
                $assignment->id => $assignment->curriculumCourse->course_name_ro
            ])
            ->toArray();
    }
}
