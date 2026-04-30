<?php

namespace App\Services;

use App\Models\CourseAssignment;
use App\Models\CourseSyllabusContent;
use App\Models\SyllabusTemplate;

class CourseSyllabusFormBuilder
{
    /**
     * Get initial form data for course assignment.
     * Constructs editable data based on the active syllabus template configuration
     * and the placeholders marked as editable within form sections.
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
     * Retrieves readonly data from the course syllabus content
     * and course assignment based on the placeholders configuration.
     *
     * This method processes sections and placeholders in the template configuration,
     * resolving and formatting output for placeholders that are marked as non-editable.
     *
     * @param CourseSyllabusContent $content The course syllabus content instance containing template and placeholders data.
     * @param CourseAssignment $assignment The course assignment associated with the syllabus content.
     *
     * @return array An associative array where keys are names of placeholders and values are their resolved and formatted output.
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
     * Retrieves the list of editable sections for a given course assignment.
     *
     * The method generates a base list of editable sections and dynamically adds
     * additional sections depending on the course components such as seminar, lab,
     * project, and practice hours associated with the curriculum course.
     *
     * @param CourseAssignment $assignment The course assignment containing curriculum course data.
     *
     * @return array An array of strings representing the names of editable sections.
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
     * Determines whether a given form section should be displayed
     * based on the curriculum course details associated with the assignment.
     *
     * This method evaluates the provided form section name and checks
     * the relevant hours (e.g., seminar, laboratory, project, practice)
     * in the curriculum course to decide if the section is applicable.
     *
     * @param string $formSection The name of the form section to evaluate.
     * @param CourseAssignment $assignment The course assignment containing curriculum course details.
     *
     * @return bool True if the form section should be shown, otherwise false.
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
     * Generates the form schema for a specified section of the course assignment.
     *
     * This method retrieves the active syllabus template for the provided academic year
     * and processes its placeholder configuration to build a schema for editable form fields
     * within the specified section.
     *
     * @param CourseAssignment $assignment The course assignment for which the form schema is generated.
     * @param string $formSection The specific form section for which placeholders should be included.
     *
     * @return array An array of form schema configurations, where each schema item
     *               includes details such as field name, label, type, required status, validation rules, and placeholder name.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no active syllabus template is found for the assignment's academic year.
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
     * Retrieves form fields configuration for a course assignment based on the syllabus template.
     *
     * This method processes the sections and placeholders in the template configuration to
     * collect details for editable placeholders that are associated with form sections.
     * The resulting array includes metadata for rendering and validating the form fields.
     *
     * @param CourseAssignment $assignment The course assignment used to determine the relevant syllabus template.
     *
     * @return array An associative array where keys are the names of placeholders and values are their form configurations,
     *               including form section, database field, input type, label, validation rules, and display order.
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
