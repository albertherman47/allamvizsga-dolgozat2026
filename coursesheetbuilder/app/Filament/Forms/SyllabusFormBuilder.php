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
     * Build the complete form schema
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
     * Build a single section tab
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
     * Build a form component based on placeholder configuration
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
     * Build text input component
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
     * Build textarea component
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
     * Build number input component
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
     * Build date input component
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
     * Build repeater component for dynamic fields
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
     * Get course information content
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
