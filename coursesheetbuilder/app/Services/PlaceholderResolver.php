<?php

namespace App\Services;

use App\Enums\UniversityInfo;
use App\Models\CourseSyllabusContent;
use App\Models\CourseAssignment;
use App\Models\CurriculumCourse;
use App\Models\Teacher;

class PlaceholderResolver
{
    private CourseSyllabusContent $content;
    private CourseAssignment $assignment;
    private ?CurriculumCourse $course;
    private ?Teacher $teacher;

    public function __construct(CourseSyllabusContent $content, CourseAssignment $assignment)
    {
        $this->content = $content;
        $this->assignment = $assignment;
        $this->course = $assignment->curriculumCourse;
        $this->teacher = $assignment->courseLeader;
    }

    /**
     * Resolves the given placeholder to a specific value based on its data source.
     *
     * This method evaluates the `data_source` key from the provided placeholder array and delegates
     * handling to the appropriate resolution method. It supports several types of data sources,
     * including static, database, computed, and editable. If no matching data source is found,
     * it defaults to returning an empty string.
     *
     * @param array $placeholder The placeholder data containing a 'data_source' key.
     * @return mixed The resolved value based on the specified data source.
     */
    public function resolve(array $placeholder): mixed
    {
        return match($placeholder['data_source']) {
            'static' => $this->resolveStatic($placeholder),
            'database' => $this->resolveDatabase($placeholder),
            'computed' => $this->resolveComputed($placeholder),
            'editable' => $this->resolveEditable($placeholder),
            default => ''
        };
    }


    private function resolveStatic(array $placeholder): string
    {
        if (isset($placeholder['enum_class']) && isset($placeholder['enum_case'])) {
            $enumCase = $placeholder['enum_case'];
            $enumLang = $placeholder['enum_lang'] ?? 'ro';

            // Access enum case by name (PHP 8.1+ pure syntax)
            $enum = UniversityInfo::{$enumCase};

            return $enum->get($enumLang);
        }

        return '';
    }

    /**
     * Resolve database values
     */
    private function resolveDatabase(array $placeholder): mixed
    {
        // If there's a source_method, call it
        if (isset($placeholder['source_method'])) {
            $method = $placeholder['source_method'];
            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        // If there's a source_table and source_column, navigate relationships
        if (isset($placeholder['source_table']) && isset($placeholder['source_column'])) {
            return $this->resolveFromRelationship($placeholder['source_table'], $placeholder['source_column']);
        }

        return '';
    }

    /**
     * Resolve computed values
     */
    private function resolveComputed(array $placeholder): mixed
    {
        if (isset($placeholder['compute_method'])) {
            $method = $placeholder['compute_method'];
            $params = $placeholder['compute_params'] ?? null;

            if (method_exists($this, $method)) {
                return $params ? $this->$method($params) : $this->$method();
            }
        }

        return '';
    }

    /**
     * Resolve editable values
     */
    private function resolveEditable(array $placeholder): mixed
    {
        $name = $placeholder['name'];
        $dbField = $placeholder['db_field'] ?? null;

        // If content is completed and has snapshot, use snapshot
        if ($this->content->status === 'completed' && $this->content->completed_snapshot) {
            if (isset($this->content->completed_snapshot[$name])) {
                return $this->content->completed_snapshot[$name];
            }
        }

        // Otherwise, get from editable_data
        $section = $placeholder['form_section'] ?? null;

        if ($section && isset($this->content->editable_data[$section])) {
            $sectionData = $this->content->editable_data[$section];

            // Try fetching by db_field first
            if ($dbField && isset($sectionData[$dbField])) {
                return $sectionData[$dbField];
            }

            // Fallback to name
            if (isset($sectionData[$name])) {
                return $sectionData[$name];
            }
        }

        return '';
    }

    /**
     * Navigate and resolve from relationships
     */
    private function resolveFromRelationship(string $sourceTable, string $sourceColumn): mixed
    {
        return match($sourceTable) {
            'academic_years' => $this->assignment->academicYear?->$sourceColumn,
            'departments' => $this->course?->curriculum?->program?->department?->$sourceColumn,
            'programs' => $this->course?->curriculum?->program?->$sourceColumn,
            'curriculum_courses' => $this->course?->$sourceColumn,
            'teachers' => $this->teacher?->$sourceColumn,
            default => ''
        };
    }

    /**
     * Calculate weekly hours
     */
    private function calculateWeeklyHours(): int
    {
        if (!$this->course) return 0;
        return ($this->course->lecture_hours ?? 0)
             + ($this->course->seminar_hours ?? 0)
             + ($this->course->lab_hours ?? 0)
             + ($this->course->project_hours ?? 0);
    }

    /**
     * Calculate total planned hours (weeks * hours per week)
     */
    private function calculateTotalPlannedHours(): int
    {
        return $this->calculateWeeklyHours() * 14;
    }

    /**
     * Calculate total course hours
     */
    private function calculateTotalCourseHours(): int
    {
        return ($this->course->lecture_hours ?? 0) * 14;
    }

    /**
     * Calculate total practical hours
     */
    private function calculateTotalPracticalHours(): int
    {
        return (($this->course->seminar_hours ?? 0)
               + ($this->course->lab_hours ?? 0)
               + ($this->course->project_hours ?? 0)) * 14;
    }

    /**
     * Calculate total online hours
     */
    private function calculateTotalOnlineHours(): int
    {
        return (($this->course->lecture_hours_online ?? 0)
               + ($this->course->seminar_hours_online ?? 0)
               + ($this->course->lab_hours_online ?? 0)
               + ($this->course->project_hours_online ?? 0)) * 14;
    }

    /**
     * Calculate online practical hours
     */
    private function calculateOnlinePracticalHours(): int
    {
        return (($this->course->seminar_hours_online ?? 0)
               + ($this->course->lab_hours_online ?? 0)
               + ($this->course->project_hours_online ?? 0)) * 14;
    }

    /**
     * Calculate semester hours
     */
    private function calculateSemesterHours(): int
    {
        return ($this->course->credits ?? 0) * 28;
    }

    /**
     * Calculate individual study hours
     */
    private function calculateIndividualStudyHours(): int
    {
        return $this->calculateSemesterHours() - $this->calculateTotalPlannedHours();
    }

    /**
     * Calculate weekly practical hours (seminar/lab/project/practice)
     */
    private function calculateWeeklyPracticalHours(): string
    {
        $parts = [];

        if (($this->course->seminar_hours ?? 0) > 0) {
            $parts[] = $this->course->seminar_hours;
        }
        if (($this->course->lab_hours ?? 0) > 0) {
            $parts[] = $this->course->lab_hours;
        }
        if (($this->course->project_hours ?? 0) > 0) {
            $parts[] = $this->course->project_hours;
        }
        if (($this->course->practice_hours ?? 0) > 0) {
            $parts[] = $this->course->practice_hours;
        }

        // If no practical hours, return 0
        if (empty($parts)) {
            return '0';
        }

        // If only one type, return just that number
        if (count($parts) === 1) {
            return (string)$parts[0];
        }

        // If multiple types, return formatted as "sem/lab/proj/prac"
        return implode('/', $parts);
    }

    /**
     * Get activity type checkmark
     */
    private function getActivityTypeCheckmark(array $params): string
    {
        $type = $params['type'] ?? '';
        $activityType = $this->course->activity_type ?? '';

        return str_contains($activityType, $type) ? '☑' : '☐';
    }

    /**
     * Get formatted course leader name
     */
    private function getFormattedCourseLeaderName(): string
    {
        return $this->teacher ? $this->teacher->getFormattedName() : '';
    }

    /**
     * Get formatted seminar leader name
     */
    private function getFormattedSeminarLeaderName(): string
    {
        $seminarTeacher = $this->assignment->seminarLeader;
        return $seminarTeacher ? $seminarTeacher->getFormattedName() : '';
    }

    /**
     * Get formatted lab leader name
     */
    private function getFormattedLabLeaderName(): string
    {
        $labTeacher = $this->assignment->labLeader;
        return $labTeacher ? $labTeacher->getFormattedName() : '';
    }

    /**
     * Get formatted project leader name
     */
    private function getFormattedProjectLeaderName(): string
    {
        $projectTeacher = $this->assignment->projectLeader;
        return $projectTeacher ? $projectTeacher->getFormattedName() : '';
    }

    /**
     * Retrieves the concatenated signatures of the application's leaders.
     *
     * This method collects and formats the names of leaders associated with
     * the assignment (e.g., seminar, lab, or project leaders) by combining
     * their academic titles and full names. If a leader's data is missing,
     * it safely handles the absence by conditionally appending available details.
     * The resulting names are joined into a single string, separated by commas.
     *
     * @return string A comma-separated string of formatted leader names.
     */
    private function getApplicationLeadersSignature(): string
    {
        $names = [];

        if ($this->assignment->seminarLeader) {
            $names[] = trim(($this->assignment->seminarLeader->academic_title ?? '') . ' ' . ($this->assignment->seminarLeader->full_name ?? ''));
        }
        if ($this->assignment->labLeader) {
            $names[] = trim(($this->assignment->labLeader->academic_title ?? '') . ' ' . ($this->assignment->labLeader->full_name ?? ''));
        }
        if ($this->assignment->projectLeader) {
            $names[] = trim(($this->assignment->projectLeader->academic_title ?? '') . ' ' . ($this->assignment->projectLeader->full_name ?? ''));
        }

        return implode(', ', $names);
    }

    /**
     * Get course leader signature
     */
    private function getCourseLeaderSignature(): string
    {
        return $this->getFormattedCourseLeaderName();
    }

    /**
     * Get department director signature
     */
    private function getDepartmentDirectorSignature(): string
    {
        $director = $this->course->curriculum->program->department?->director;
        if (!$director) {
            return '';
        }
        return trim(($director->academic_title ?? '') . ' ' . ($director->full_name ?? ''));
    }

    /**
     * Get program coordinator signature
     */
    private function getProgramCoordinatorSignature(): string
    {
        $coordinator = $this->course->curriculum->program?->coordinator;
        if (!$coordinator) {
            return '';
        }
        return trim(($coordinator->academic_title ?? '') . ' ' . ($coordinator->full_name ?? ''));
    }

    /**
     * Get current date
     */
    private function getCurrentDate(): string
    {
        return now()->format('d.m.Y');
    }

    /**
     * Formats the given value into a specified output format based on the placeholder configuration.
     *
     * This method supports various formats such as bullet lists, numbered lists, table rows, and plain text.
     * It processes the value accordingly, extracting fields if necessary, and delegates to specific format methods.
     * If no format is specified or an unsupported format is provided, the default plain text format is used.
     *
     * @param mixed $value The data to be formatted. It may be a single value or an array.
     * @param array $placeholder An associative array containing formatting options and additional configurations.
     *                            Expected keys:
     *                              - 'output_format' (string): Specifies the desired format (e.g., 'bullet_list', 'numbered_list', etc.).
     *                              - 'list_field' (string|null): The specific field to extract from array elements when applicable.
     * @return string The formatted representation of the input value based on the specified output format.
     */
    public function formatOutput(mixed $value, array $placeholder): string
    {
        // For simple arrays coming from repeaters, we might need to extract the string
        $listField = $placeholder['list_field'] ?? null;

        return match($placeholder['output_format'] ?? 'text') {
            'bullet_list' => $this->formatBulletList($value, $listField),
            'numbered_list' => $this->formatNumberedList($value, $listField),
            'table_row' => $this->formatTableRow($value),
            'text' => $this->formatText($value, $listField),
            default => $this->formatText($value, $listField)
        };
    }

    /**
     * Formats the provided value into a string representation.
     *
     * If the input value is not an array, it is cast to a string.
     * If it is an array, the method processes each item, and depending on whether
     * `listField` is provided, extracts the specified field or the first value of the array.
     * The processed items are then combined into a single string with newline characters separating them.
     *
     * @param mixed $value The value to be formatted. Can be an array or other data type.
     * @param string|null $listField The key to extract from array items, if applicable.
     * @return string The formatted string representation of the input value.
     */
    private function formatText(mixed $value, ?string $listField): string
    {
        if (!is_array($value)) {
            return (string) $value;
        }

        // If it's an array from a repeater, extract the field and implode
        $extracted = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $extracted[] = $listField ? ($item[$listField] ?? '') : reset($item);
            } else {
                $extracted[] = $item;
            }
        }

        return implode("\n", array_filter($extracted));
    }

    /**
     * Formats the provided value into a bullet list string representation.
     *
     * If the input value is not an array, it is cast to a string. If it is an array,
     * the method processes each item, extracting the specified field if `listField`
     * is provided, or the first value of the array otherwise. Each valid item is
     * prefixed with a bullet ('•') and combined into a single string separated by
     * newline characters.
     *
     * @param mixed $items The value to be formatted. Can be an array or other data type.
     * @param string|null $listField The key to extract from array items, if applicable.
     * @return string The formatted bullet list string representation of the input value.
     */
    private function formatBulletList(mixed $items, ?string $listField): string
    {
        if (!is_array($items)) {
            return (string) $items;
        }

        $result = [];
        foreach (array_filter($items) as $item) {
            $val = is_array($item) ? ($listField ? ($item[$listField] ?? '') : reset($item)) : $item;
            if (!empty($val)) {
                $result[] = '• ' . $val;
            }
        }
        return implode("\n", $result);
    }

    /**
     * Formats the provided array of items into a numbered list string.
     *
     * If the input is not an array, it is cast to a string. For arrays, each item is processed
     * to extract the specified field (if `listField` is provided) or the first value of the array,
     * when applicable. Valid items are prefixed with their respective position number in the list,
     * and the resulting items are joined with newline characters.
     *
     * @param mixed $items The value to be formatted, ideally an array of items.
     * @param string|null $listField The key to extract from each array item, if provided.
     * @return string A newline-separated, numbered list representation of the items.
     */
    private function formatNumberedList(mixed $items, ?string $listField): string
    {
        if (!is_array($items)) {
            return (string) $items;
        }

        $result = [];
        foreach (array_filter($items) as $item) {
            $val = is_array($item) ? ($listField ? ($item[$listField] ?? '') : reset($item)) : $item;
            if (!empty($val)) {
                $result[] = (count($result) + 1) . '. ' . $val;
            }
        }
        return implode("\n", $result);
    }

    /**
     * Formats the provided items into a markdown table row representation.
     *
     * If the input is not an array, it is cast to a string and returned.
     * If it is an array, each item is processed and converted into a single row of a markdown-styled table.
     * Nested arrays are concatenated using a pipe (`|`) separator for each row.
     * Non-array items are cast to strings and included as-is.
     *
     * @param mixed $items The items to be formatted. Can be an array of data or a single value.
     * @return string The formatted markdown table row as a string.
     */
    private function formatTableRow(mixed $items): string
    {
        if (!is_array($items)) {
            return (string) $items;
        }

        // Convert array of items to markdown table format
        $result = [];
        foreach (array_filter($items) as $item) {
            if (is_array($item)) {
                $result[] = implode(' | ', $item);
            } else {
                $result[] = (string) $item;
            }
        }
        return implode("\n", $result);
    }
}
