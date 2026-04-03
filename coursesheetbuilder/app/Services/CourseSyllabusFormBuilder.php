<?php

namespace App\Services;

use App\Models\CourseAssignment;
use App\Models\CourseSyllabusContent;
use App\Models\SyllabusTemplate;

class CourseSyllabusFormBuilder
{
    /**
     * Get initial form data structure for a new syllabus
     */
    public function getInitialFormData(CourseAssignment $assignment): array
    {
        $template = SyllabusTemplate::where('academic_year_id', $assignment->curriculumCourse->curriculum->academic_year_id)
            ->where('is_active', true)
            ->firstOrFail();

        $editable_data = [];

        // Iterate through template sections
        foreach ($template->placeholders_config['sections'] as $sectionKey => $section) {
            foreach ($section['placeholders'] as $placeholder) {
                if ($placeholder['is_editable'] === true && !empty($placeholder['form_section'])) {
                    $formSection = $placeholder['form_section'];
                    $dbField = $placeholder['db_field'];

                    // Initialize form section if not exists
                    if (!isset($editable_data[$formSection])) {
                        $editable_data[$formSection] = [];
                    }

                    // For repeater type, use empty array; otherwise empty string
                    if (($placeholder['form_type'] ?? null) === 'repeater') {
                        $editable_data[$formSection][$dbField] = [];
                    } else {
                        $editable_data[$formSection][$dbField] = '';
                    }
                }
            }
        }

        return ['editable_data' => $editable_data];
    }

    /**
     * Get readonly data (computed, static, database values)
     */
    public function getReadonlyData(CourseSyllabusContent $content, CourseAssignment $assignment): array
    {
        $template = SyllabusTemplate::find($content->template_id);
        $resolver = new PlaceholderResolver($content, $assignment);

        $readonlyData = [];

        foreach ($template->placeholders_config['sections'] as $section) {
            foreach ($section['placeholders'] as $placeholder) {
                if ($placeholder['is_editable'] === false) {
                    $value = $resolver->resolve($placeholder);
                    $readonlyData[$placeholder['name']] = $resolver->formatOutput($value, $placeholder);
                }
            }
        }

        return $readonlyData;
    }

    /**
     * Get list of editable form sections for a course assignment
     */
    public function getEditableSections(CourseAssignment $assignment): array
    {
        $sections = [
            'meta',
            'individual_study',
            'prerequisites',
            'conditions',
            'objectives',
            'content_course',
            'alignment',
            'evaluation',
            'signatures'
        ];

        // Conditional sections based on course components
        if (($assignment->curriculumCourse->seminar_hours ?? 0) > 0) {
            $sections[] = 'content_seminar';
        }

        if (($assignment->curriculumCourse->lab_hours ?? 0) > 0) {
            $sections[] = 'content_laboratory';
        }

        if (($assignment->curriculumCourse->project_hours ?? 0) > 0) {
            $sections[] = 'content_project';
        }

        if (($assignment->curriculumCourse->practice_hours ?? 0) > 0) {
            $sections[] = 'content_practice';
        }

        return $sections;
    }

    /**
     * Check if a form section should be shown for a course assignment
     */
    public function shouldShowSection(string $formSection, CourseAssignment $assignment): bool
    {
        return match($formSection) {
            'content_seminar' => ($assignment->curriculumCourse->seminar_hours ?? 0) > 0,
            'content_laboratory' => ($assignment->curriculumCourse->lab_hours ?? 0) > 0,
            'content_project' => ($assignment->curriculumCourse->project_hours ?? 0) > 0,
            'content_practice' => ($assignment->curriculumCourse->practice_hours ?? 0) > 0,
            default => true
        };
    }

    /**
     * Get form schema for a specific section
     */
    public function getSectionFormSchema(CourseAssignment $assignment, string $formSection): array
    {
        $template = SyllabusTemplate::where('academic_year_id', $assignment->curriculumCourse->curriculum->academic_year_id)
            ->where('is_active', true)
            ->firstOrFail();

        $schema = [];

        foreach ($template->placeholders_config['sections'] as $section) {
            foreach ($section['placeholders'] as $placeholder) {
                if ($placeholder['form_section'] === $formSection && $placeholder['is_editable'] === true) {
                    $schema[] = [
                        'name' => $placeholder['db_field'],
                        'label' => $placeholder['description'],
                        'type' => $placeholder['form_type'] ?? 'text',
                        'required' => $placeholder['validation']['required'] ?? false,
                        'validation' => $placeholder['validation'] ?? [],
                        'placeholder' => $placeholder['name'],
                    ];
                }
            }
        }

        return $schema;
    }

    /**
     * Get all form fields for editing
     */
    public function getFormFields(CourseAssignment $assignment): array
    {
        $template = SyllabusTemplate::where('academic_year_id', $assignment->curriculumCourse->curriculum->academic_year_id)
            ->where('is_active', true)
            ->firstOrFail();

        $fields = [];

        foreach ($template->placeholders_config['sections'] as $section) {
            foreach ($section['placeholders'] as $placeholder) {
                if ($placeholder['is_editable'] === true && !empty($placeholder['form_section'])) {
                    $fields[$placeholder['name']] = [
                        'form_section' => $placeholder['form_section'],
                        'db_field' => $placeholder['db_field'],
                        'type' => $placeholder['form_type'] ?? 'text',
                        'label' => $placeholder['description'],
                        'validation' => $placeholder['validation'] ?? [],
                        'display_order' => $placeholder['display_order'] ?? 999,
                    ];
                }
            }
        }

        return $fields;
    }
}
