<?php

namespace App\Filament\Resources\Syllabi\Pages;

use App\Filament\Resources\Syllabi\CourseSyllabusContentResource;
use App\Services\CourseSyllabusFormBuilder;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseSyllabusContent extends CreateRecord
{
    protected static string $resource = CourseSyllabusContentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Initialize editable_data with form structure if not present
        if (empty($data['editable_data'])) {
            $courseAssignmentId = $data['course_assignment_id'] ?? null;
            if ($courseAssignmentId) {
                $courseAssignment = \App\Models\CourseAssignment::find($courseAssignmentId);
                if ($courseAssignment) {
                    $builder = new CourseSyllabusFormBuilder();
                    $initialData = $builder->getInitialFormData($courseAssignment);
                    $data['editable_data'] = $initialData['editable_data'];
                }
            }
        }

        // Set default status
        $data['status'] = $data['status'] ?? 'draft';

        return $data;
    }
}
