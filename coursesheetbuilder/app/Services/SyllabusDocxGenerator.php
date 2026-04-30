<?php

namespace App\Services;

use App\Models\CourseSyllabusContent;
use App\Models\CourseAssignment;
use PhpOffice\PhpWord\TemplateProcessor;

class SyllabusDocxGenerator
{
    private CourseSyllabusContent $content;
    private CourseAssignment $assignment;
    private PlaceholderResolver $resolver;
    private TemplateProcessor $template;

    /**
     *
     */
    public function __construct(CourseSyllabusContent $content, CourseAssignment $assignment)
    {
        $this->content = $content;
        $this->assignment = $assignment;
        $this->resolver = new PlaceholderResolver($content, $assignment);

        // Load the template
        $templatePath = base_path('template_fisa_disciplinei_2025.docx');
        if (!file_exists($templatePath)) {
            throw new \Exception('Syllabus template not found at: ' . $templatePath);
        }

        $this->template = new TemplateProcessor($templatePath);
    }

    /**
     * Generate and return DOCX file path
     */
    public function generate(): string
    {
        $this->fillTemplate();
        return $this->saveDocument();
    }

    /**
     * Populates a template with dynamically generated content based on placeholders.
     *
     * This method processes all sections and their placeholders in the provided template.
     * It ensures placeholders are ordered correctly and processes special formats
     * like table rows last to avoid disruption of other variables in the same row.
     * Finally, any unmatched template variables are cleaned up.
     *
     * @throws \Exception If no template is found for the current syllabus.
     */
    private function fillTemplate(): void
    {
        $template = $this->content->template;
        if (!$template) {
            throw new \Exception('Template not found for this syllabus');
        }

        // Get all sections with placeholders
        $sections = $template->getSections();
        usort($sections, fn($a, $b) => $a['display_order'] <=> $b['display_order']);

        // Fill placeholders for each section
        foreach ($sections as $section) {
            $placeholders = $section['placeholders'];
            usort($placeholders, fn($a, $b) => $a['display_order'] <=> $b['display_order']);

            // Sort so table_row variables are processed LAST
            // This prevents cloneRowAndSetValues from wiping out neighboring variables in the same row
            usort($placeholders, function($a, $b) {
                $isTableA = ($a['output_format'] ?? 'text') === 'table_row' ? 1 : 0;
                $isTableB = ($b['output_format'] ?? 'text') === 'table_row' ? 1 : 0;

                if ($isTableA !== $isTableB) {
                    return $isTableA <=> $isTableB;
                }

                return $a['display_order'] <=> $b['display_order'];
            });

            foreach ($placeholders as $placeholder) {
                $this->fillPlaceholder($placeholder);
            }
        }

        // Clean up any remaining unmatched template variables
        $variables = $this->template->getVariables();
        foreach ($variables as $variable) {
            $this->template->setValue($variable, '');
        }
    }

    /**
     * Fills a specific placeholder in the template with processed data.
     *
     * This method resolves the value of the provided placeholder using a resolver.
     * It supports replacing placeholders with text values or handling table row formatting
     * for dynamic content generation. The resolved and formatted value is then sanitized
     * and set into the corresponding placeholder within the template.
     *
     * @param array $placeholder An associative array containing details about the placeholder,
     *                           including its name and additional formatting options.
     *
     * @throws \Exception If any error occurs during value resolution or formatting.
     */
    private function fillPlaceholder(array $placeholder): void
    {
        $name = $placeholder['name'];

        try {
            // Resolve the value
            $value = $this->resolver->resolve($placeholder);

            // If it's a table row format, we need to clone rows
            if (($placeholder['output_format'] ?? 'text') === 'table_row') {
                $this->fillTableRow($name, $value, $placeholder);
                return;
            }

            // Format the output
            $formattedValue = $this->resolver->formatOutput($value, $placeholder);

            // Replace placeholder in template
            // Clean the value for Word
            $cleanValue = $this->cleanForWord($formattedValue);

            // Use 'name' to set the placeholder (this is what's in the DOCX template)
            $this->template->setValue($name, $cleanValue);
        } catch (\Exception $e) {
            // If placeholder doesn't exist in template, just skip it
            // This allows templates to have fewer fields than we have data for
        }
    }

    /**
     * Populates a table template variable in a Word document with data.
     *
     * @param string $placeholderName The placeholder name in the template to replace with table data.
     * @param mixed $items The data to populate in the table. Can be an array of items or a single value.
     * @param array $config Configuration array containing details about table columns.
     *
     * Populates table rows in the template based on the provided data and configuration. If the data is
     * empty or not*/
    private function fillTableRow(string $placeholderName, mixed $items, array $config): void
    {

        $columns = $config['table_columns'] ?? [];
        if (empty($columns)) {
            $this->template->setValue($placeholderName, is_array($items) ? implode(', ', $items) : (string)$items);
            return;
        }

        if (!is_array($items) || empty($items)) {
            // Provide at least one empty row so variables don't remain in doc
            $emptyRow = [];
            foreach ($columns as $col) {
                $emptyRow[$col['placeholder']] = '';
            }
            $this->template->cloneRowAndSetValues($placeholderName, [$emptyRow]);
            return;
        }

        $values = [];
        foreach ($items as $item) {
            $row = [];
            foreach ($columns as $col) {
                $field = $col['field'];
                $ph = $col['placeholder'];
                $val = is_array($item) ? ($item[$field] ?? '') : $item;
                $row[$ph] = $this->cleanForWord((string)$val);
            }
            $values[] = $row;
        }

        $this->template->cloneRowAndSetValues($placeholderName, $values);
    }

    /**
     * Cleans and formats a string for use in a Word document.
     *
     * This function is designed to sanitize and prepare text so that it can be
     * safely and correctly inserted into a Word document. It handles decoding
     * HTML entities, escaping XML characters to prevent DOCX corruption, removing
     * specific formatting such as bullet points, and replacing newlines with Word-compatible
     * line breaks.
     *
     * @param string $value The input string to be sanitized and formatted.
     * @return string The sanitized and formatted string ready for use in a Word document.
     */
    private function cleanForWord(string $value): string
    {
        // Decode first just in case there are HTML entities like &nbsp; or &amp; already there
        $value = htmlspecialchars_decode($value, ENT_QUOTES);

        // Remove bullet points for simpler display
        $value = str_replace('•', '-', $value);

        // Escape for XML! This prevents DOCX corruption if user types "&" or "<"
        // DOCX is essentially XML.
        $value = htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        // Replace newlines with Word line breaks
        // We do this AFTER htmlspecialchars so the < and > in our tags aren't escaped.
        $value = str_replace("\n", "</w:t><w:br/><w:t>", $value);

        return $value;
    }

    /**
     * Saves the generated syllabus document to a designated storage path.
     *
     * @return string The file path of the saved document.
     * @throws \Exception If the document could not be saved or if a required directory cannot be created.
     */
    private function saveDocument(): string
    {
        $courseCode = $this->assignment->curriculumCourse->course_code ?? 'UNKNOWN';
        $timestamp = now()->format('YmdHis');
        $filename = "{$courseCode}_syllabus_{$timestamp}.docx";

        $filePath = storage_path("app/syllabi/{$filename}");

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        // Save the document
        $this->template->saveAs($filePath);

        return $filePath;
    }
}
