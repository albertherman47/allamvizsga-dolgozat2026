<?php

namespace App\Filament\Forms;

use App\Services\SyllabusService;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SyllabusFormBuilder
{
    protected $syllabusService;

    public function __construct(SyllabusService $syllabusService)
    {
        $this->syllabusService = $syllabusService;
    }

    /**
     * Builds and returns the schema with the configured components, such as course information
     * and dynamically generated tabs for the syllabus form.
     *
     * @param Schema $schema The schema instance used to define the components and structure.
     * @return Schema The modified schema object with the added components.
     */
    public function buildSchema(Schema $schema): Schema
    {
        $tabs = [];
        $formSections = $this->syllabusService->getFormSections();

        foreach ($formSections as $sectionKey => $sectionLabel) {
            $tabs[] = $this->buildSectionTab($sectionKey, $sectionLabel);
        }

        return $schema
            ->components([
                Placeholder::make('course_info')
                    ->label('Course Information')
                    ->content(fn ($record) => $this->getCourseInfoContent()),

                Tabs::make('Syllabus Form')
                    ->tabs($tabs),
            ]);
    }

    /**
     * Builds and returns a tab for the given section, including dynamically generated components
     * based on editable placeholders obtained for that section.
     *
     * @param string $sectionKey The key identifying the section.
     * @param string $sectionLabel The display label for the section tab.
     * @return Tabs\Tab The tab instance with its schema populated by the generated components.
     */
    protected function buildSectionTab(string $sectionKey, string $sectionLabel): Tabs\Tab
    {
        $components = [];
        $editablePlaceholders = $this->syllabusService
            ->getDataProvider()
            ->getEditablePlaceholdersBySection($sectionKey);

        foreach ($editablePlaceholders as $placeholder) {
            $component = $this->buildFormComponent($placeholder);
            if ($component) {
                $components[] = $component;
            }
        }

        return Tabs\Tab::make($sectionLabel)
            ->schema($components);
    }

    /**
     * Builds and returns a form component based on the specified placeholder configuration.
     *
     * The method determines the form component type (e.g., text input, textarea, number input, date input, or repeater)
     * and constructs it using the provided placeholder details such as short name, description, validation rules,
     * and additional configurations.
     *
     * @param array $placeholder The configuration for the form component including details such as short name,
     *                           description, form type, and validation rules.
     * @return mixed The constructed form component corresponding to the form type, or null for unsupported or
     *               computed types.
     */
    protected function buildFormComponent(array $placeholder)
    {
        $shortName = $placeholder['short_name'];
        $description = $placeholder['description'];
        $formType = $placeholder['form_type'] ?? 'text';

        $validation = $placeholder['validation'] ?? [];
        $isRequired = $validation['required'] ?? false;
        $maxLength = $validation['max_length'] ?? null;

        return match ($formType) {
            'text' => $this->buildTextInput($shortName, $description, $isRequired, $maxLength),
            'textarea' => $this->buildTextarea($shortName, $description, $isRequired, $maxLength),
            'number' => $this->buildNumberInput($shortName, $description, $isRequired, $validation),
            'date' => $this->buildDateInput($shortName, $description, $isRequired),
            'repeater' => $this->buildRepeater($shortName, $description, $placeholder),
            'computed_from_repeater' => null, // Skip - computed field
            default => null,
        };
    }

    /**
     * Creates and returns a configured text input field.
     *
     * @param string $name The name or key for the input field.
     * @param string $label The label for the text input field.
     * @param bool $required Indicates whether the input field is required.
     * @param int|null $maxLength The maximum allowed length for the input field. Null if no limit applies.
     * @return TextInput The configured text input instance with the specified properties.
     */
    protected function buildTextInput(string $name, string $label, bool $required, ?int $maxLength)
    {
        return TextInput::make("editable_data.{$name}")
            ->label($label)
            ->required($required)
            ->maxLength($maxLength)
            ->columnSpanFull();
    }

    /**
     * Builds and configures a textarea field with specified attributes, such as name, label,
     * validation requirements, and maximum character length.
     *
     * @param string $name The name or key used to identify the textarea field.
     * @param string $label The display label for the textarea field.
     * @param bool $required Indicates whether the textarea field is mandatory.
     * @param int|null $maxLength The maximum character length allowed for the textarea field. Null if no limit is set.
     * @return Textarea The configured textarea component.
     */
    protected function buildTextarea(string $name, string $label, bool $required, ?int $maxLength)
    {
        return Textarea::make("editable_data.{$name}")
            ->label($label)
            ->required($required)
            ->maxLength($maxLength)
            ->rows(4)
            ->columnSpanFull();
    }

    /**
     * Builds and returns a configured numeric input field as a TextInput component.
     *
     * The input is dynamically configured with properties such as label, required status,
     * and additional validation rules (e.g., minimum and maximum values).
     *
     * @param string $name The name of the input field, used for binding to editable data.
     * @param string $label The label for the input field, displayed to the user.
     * @param bool $required Specifies whether the input field is mandatory.
     * @param array $validation An array of validation rules, such as 'min' and 'max' values.
     * @return TextInput The configured TextInput component representing the numeric input field.
     */
    protected function buildNumberInput(string $name, string $label, bool $required, array $validation)
    {
        $component = TextInput::make("editable_data.{$name}")
            ->label($label)
            ->numeric()
            ->required($required)
            ->columnSpanFull();

        if (isset($validation['min'])) {
            $component = $component->minValue($validation['min']);
        }

        if (isset($validation['max'])) {
            $component = $component->maxValue($validation['max']);
        }

        return $component;
    }

    /**
     * Constructs and returns a date input field for a form, with specific attributes
     * such as label, required status, and full column span.
     *
     * @param string $name The name of the input field, used as part of the data binding key.
     * @param string $label The label displayed for the input field.
     * @param bool $required Determines whether the input field is mandatory.
     * @return TextInput The configured date input field instance.
     */
    protected function buildDateInput(string $name, string $label, bool $required)
    {
        return TextInput::make("editable_data.{$name}")
            ->label($label)
            ->type('date')
            ->required($required)
            ->columnSpanFull();
    }

    /**
     * Builds a repeater component with the specified configuration, including dynamic fields
     * based on the provided placeholder data. The repeater allows adding, editing, and collapsing
     * of repeated field groups.
     *
     * @param string $name The name identifier for the repeater component.
     * @param string $label The label to display for the repeater title and actions.
     * @param array $placeholder The placeholder configuration containing field definitions.
     * @return Repeater The configured repeater component with dynamic fields and actions.
     */
    protected function buildRepeater(string $name, string $label, array $placeholder)
    {
        $repeaterFields = $placeholder['repeater_fields'] ?? [];
        $schema = [];

        foreach ($repeaterFields as $field) {
            $fieldName = $field['name'];
            $fieldLabel = $field['label'];
            $fieldType = $field['type'] ?? 'text';
            $placeholder = $field['placeholder'] ?? null;

            $component = match ($fieldType) {
                'textarea' => Textarea::make($fieldName)
                    ->label($fieldLabel)
                    ->placeholder($placeholder)
                    ->rows(3),
                'text' => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->placeholder($placeholder),
                default => TextInput::make($fieldName)
                    ->label($fieldLabel)
                    ->placeholder($placeholder),
            };

            $schema[] = $component->columnSpanFull();
        }

        return Repeater::make("editable_data.{$name}")
            ->label($label)
            ->schema($schema)
            ->addActionLabel('Add ' . $label)
            ->collapsible()
            ->collapsed()
            ->columnSpanFull();
    }

    /**
     * Retrieves and generates the HTML content for the course information by utilizing the data
     * provided from the syllabus service. The formatted data includes course name, academic year,
     * credits, and type, with default values if data is unavailable.
     *
     * @return string The formatted HTML content containing information about the course.
     */
    protected function getCourseInfoContent(): string
    {
        $allData = $this->syllabusService->getDataProvider()->getAllPlaceholderData();

        return sprintf(
            '<div class="space-y-2">
                <p><strong>Course:</strong> %s</p>
                <p><strong>Academic Year:</strong> %s</p>
                <p><strong>Credits:</strong> %s</p>
                <p><strong>Type:</strong> %s</p>
            </div>',
            $allData['disc_name'] ?? 'N/A',
            $allData['year'] ?? 'N/A',
            $allData['credits'] ?? 'N/A',
            $allData['disc_type'] ?? 'N/A'
        );
    }
}
