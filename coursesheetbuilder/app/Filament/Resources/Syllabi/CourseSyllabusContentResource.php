<?php

namespace App\Filament\Resources\Syllabi;

use App\Filament\Resources\Syllabi\Pages\CreateCourseSyllabusContent;
use App\Filament\Resources\Syllabi\Pages\EditCourseSyllabusContent;
use App\Filament\Resources\Syllabi\Pages\ListCourseSyllabusContents;
use App\Models\CourseSyllabusContent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Select;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CourseSyllabusContentResource extends Resource
{
    protected static ?string $model = CourseSyllabusContent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Course Syllabi';

    protected static \UnitEnum|string|null $navigationGroup = 'Teaching';

    protected static ?int $navigationSort = 2;

   

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                // Step 1: Info (Read-only Program & Discipline Info)
                Wizard\Step::make('info')
                    ->label('1. Date despre program')
                    ->schema(function (?CourseSyllabusContent $record) {
                        if (!$record || !$record->courseAssignment) {
                            return [
                                Placeholder::make('no_assignment')
                                    ->content('Please create the course assignment first.'),
                            ];
                        }

                        $builder = new \App\Services\CourseSyllabusFormBuilder();
                        $readonlyData = $builder->getReadonlyData($record, $record->courseAssignment);

                        // Get template to access config
                        $template = \App\Models\SyllabusTemplate::find($record->template_id);
                        if (!$template) {
                            return [Placeholder::make('error')->content('Template not found')];
                        }

                        // Build program section dynamically from config
                        $programSchema = [];
                        $programPlaceholders = $template->getPlaceholdersBySection('1_program_info');
                        usort($programPlaceholders, fn($a, $b) => $a['display_order'] <=> $b['display_order']);

                        foreach ($programPlaceholders as $placeholder) {
                            $name = $placeholder['name'];
                            $programSchema[] = Placeholder::make($name)
                                ->label($placeholder['description'])
                                ->content($readonlyData[$name] ?? '-');
                        }

                        // Build discipline section dynamically from config
                        $disciplineSchema = [];
                        $disciplinePlaceholders = $template->getPlaceholdersBySection('2_discipline_info');
                        usort($disciplinePlaceholders, fn($a, $b) => $a['display_order'] <=> $b['display_order']);

                        foreach ($disciplinePlaceholders as $placeholder) {
                            $name = $placeholder['name'];
                            $disciplineSchema[] = Placeholder::make($name)
                                ->label($placeholder['description'])
                                ->content($readonlyData[$name] ?? '-');
                        }

                        return [
                            Section::make('1. Date despre program')
                                ->schema($programSchema)
                                ->columns(2),

                            Section::make('2. Date despre disciplină')
                                ->schema($disciplineSchema)
                                ->columns(2),
                        ];
                    })
                    ->columns(1),

                // Step 2: Time Allocation
                Wizard\Step::make('time')
                    ->label('3. Timpul total estimat')
                    ->schema(function (?CourseSyllabusContent $record) {
                        if (!$record) {
                            return [];
                        }

                        $builder = new \App\Services\CourseSyllabusFormBuilder();
                        $readonlyData = $builder->getReadonlyData($record, $record->courseAssignment);

                        // Get template to access config
                        $template = \App\Models\SyllabusTemplate::find($record->template_id);
                        if (!$template) {
                            return [];
                        }

                        // Build readonly time allocation schema (3.1-3.6)
                        $timeAllocationSchema = [];
                        $timeAllocationPlaceholders = $template->getPlaceholdersBySection('3_time_allocation');
                        usort($timeAllocationPlaceholders, fn($a, $b) => $a['display_order'] <=> $b['display_order']);

                        // Only add readonly placeholders (is_editable: false)
                        foreach ($timeAllocationPlaceholders as $placeholder) {
                            if ($placeholder['is_editable'] === false) {
                                $name = $placeholder['name'];
                                $timeAllocationSchema[] = Placeholder::make($name)
                                    ->label($placeholder['description'])
                                    ->content($readonlyData[$name] ?? '-');
                            }
                        }

                        // Build editable individual study schema (3.10)
                        $editableSchema = [];
                        foreach ($timeAllocationPlaceholders as $placeholder) {
                            if ($placeholder['is_editable'] === true && $placeholder['form_section'] === 'individual_study') {
                                $name = $placeholder['name'];
                                $editableSchema[] = TextInput::make('editable_data.individual_study.' . $name)
                                    ->label($placeholder['description'])
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->suffix('ore');
                            }
                        }

                        $sections = [
                            Section::make('3. Timpul total estimat')
                                ->schema($timeAllocationSchema)
                                ->columns(2),
                        ];

                        if (!empty($editableSchema)) {
                            $sections[] = Section::make('3.10. Distribuția fondului de timp pentru studiu individual')
                                ->schema($editableSchema)
                                ->columns(2);
                        }

                        return $sections;
                    }),

                // Step 3: Prerequisites & Conditions
                Wizard\Step::make('prerequisites')
                    ->label('Prerequisites & Conditions')
                    ->schema([
                        Section::make('4. Precondiții')
                            ->schema([
                                Textarea::make('editable_data.prerequisites.prereq_curr')
                                    ->label('4.1. De curriculum')
                                    ->rows(3)
                                    ->placeholder('Nu este'),
                                Textarea::make('editable_data.prerequisites.prereq_comp')
                                    ->label('4.2. De competențe')
                                    ->rows(3)
                                    ->placeholder('HTML, CSS, JavaScript'),
                            ])
                            ->columns(1),

                        Section::make('5. Condiții')
                            ->schema([
                                Textarea::make('editable_data.conditions.cond_course')
                                    ->label('5.1. De desfășurare a cursului')
                                    ->rows(3),
                                Textarea::make('editable_data.conditions.cond_pract')
                                    ->label('5.2. De desfășurare a seminarului/laboratorului/proiectului/practicii')
                                    ->rows(3),
                            ])
                            ->columns(1),
                    ]),

                // Step 4: Learning Outcomes (Read-only)
                Wizard\Step::make('learning_outcomes')
                    ->label('Learning Outcomes')
                    ->schema(function (?CourseSyllabusContent $record) {
                        if (!$record || !$record->courseAssignment) {
                            return [];
                        }

                        $course = $record->courseAssignment->curriculumCourse;

                        // Handle both string and array formats
                        $knowledge = $course->learning_outcomes_knowledge;
                        if (is_string($knowledge)) {
                            $knowledge = json_decode($knowledge, true) ?? [];
                        } elseif (!is_array($knowledge)) {
                            $knowledge = [];
                        }

                        $skills = $course->learning_outcomes_skills;
                        if (is_string($skills)) {
                            $skills = json_decode($skills, true) ?? [];
                        } elseif (!is_array($skills)) {
                            $skills = [];
                        }

                        $responsibility = $course->learning_outcomes_responsibility;
                        if (is_string($responsibility)) {
                            $responsibility = json_decode($responsibility, true) ?? [];
                        } elseif (!is_array($responsibility)) {
                            $responsibility = [];
                        }

                        return [
                            Placeholder::make('learn_know')
                                ->label('6. Cunoștințe')
                                ->content('• ' . implode("\n• ", $knowledge ?: ['Nu sunt specificate'])),

                            Placeholder::make('learn_skills')
                                ->label('6. Aptitudini')
                                ->content('• ' . implode("\n• ", $skills ?: ['Nu sunt specificate'])),

                            Placeholder::make('learn_resp')
                                ->label('6. Responsabilitate și autonomie')
                                ->content('• ' . implode("\n• ", $responsibility ?: ['Nu sunt specificate'])),
                        ];
                    }),

                // Step 5: Objectives
                Wizard\Step::make('objectives')
                    ->label('Objectives')
                    ->schema([
                        Textarea::make('editable_data.objectives.obj_gen')
                            ->label('7.1. Obiectivul general al disciplinei')
                            ->required()
                            ->rows(5),
                        Textarea::make('editable_data.objectives.obj_spec')
                            ->label('7.2. Obiectivele specifice')
                            ->required()
                            ->rows(5),
                    ]),

                // Step 6: Course Content
                Wizard\Step::make('content_course')
                    ->label('Course Content')
                    ->schema([
                        Section::make('8.1. Curs - Teme')
                            ->schema([
                                Repeater::make('editable_data.content_course.course_topics')
                                    ->label('Course Topics')
                                    ->schema([
                                        Textarea::make('topic')
                                            ->label('Temă')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpan(2),
                                        TextInput::make('hours')
                                            ->label('Ore')
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă temă'),
                            ]),

                        Section::make('8.1. Curs - Metode de predare')
                            ->schema([
                                Textarea::make('editable_data.content_course.course_teaching_methods')
                                    ->label('Metode de predare')
                                    ->rows(3)
                                    ->placeholder('Expunere: descriere, explicaţii, exemple practice, demonstraţii.'),
                            ]),

                        Section::make('8.1. Curs - Bibliografie')
                            ->schema([
                                Repeater::make('editable_data.content_course.bibliography_course')
                                    ->label('References')
                                    ->schema([
                                        Textarea::make('citation')
                                            ->label('Citare bibliografică')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă referință'),
                            ]),
                    ]),

                // Step 7: Laboratory Content (Conditional)
                Wizard\Step::make('content_laboratory')
                    ->label('Laboratory Content')
                    ->schema([
                        Section::make('8.3. Laborator - Sarcini')
                            ->schema([
                                Repeater::make('editable_data.content_laboratory.laboratory_tasks')
                                    ->label('Laboratory Tasks')
                                    ->schema([
                                        Textarea::make('task')
                                            ->label('Sarcină')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpan(2),
                                        TextInput::make('hours')
                                            ->label('Ore')
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă sarcină'),
                            ]),

                        Section::make('8.3. Laborator - Metode de predare')
                            ->schema([
                                Textarea::make('editable_data.content_laboratory.laboratory_teaching_methods')
                                    ->label('Metode de predare')
                                    ->rows(3),
                            ]),

                        Section::make('8.3. Laborator - Bibliografie')
                            ->schema([
                                Repeater::make('editable_data.content_laboratory.bibliography_laboratory')
                                    ->label('References')
                                    ->schema([
                                        Textarea::make('citation')
                                            ->label('Citare bibliografică')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă referință'),
                            ]),
                    ])
                    ->hidden(fn(?CourseSyllabusContent $record) =>
                        !$record || !$record->courseAssignment || ($record->courseAssignment->curriculumCourse->lab_hours ?? 0) == 0
                    ),

                // Step 7.1: Seminar Content (Conditional)
                Wizard\Step::make('content_seminar')
                    ->label('Seminar Content')
                    ->schema([
                        Section::make('8.2. Seminar - Teme')
                            ->schema([
                                Repeater::make('editable_data.content_seminar.seminar_topics')
                                    ->label('Seminar Topics')
                                    ->schema([
                                        Textarea::make('topic')
                                            ->label('Temă')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpan(2),
                                        TextInput::make('hours')
                                            ->label('Ore')
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă temă'),
                            ]),

                        Section::make('8.2. Seminar - Metode de predare')
                            ->schema([
                                Textarea::make('editable_data.content_seminar.seminar_teaching_methods')
                                    ->label('Metode de predare')
                                    ->rows(3),
                            ]),

                        Section::make('8.2. Seminar - Bibliografie')
                            ->schema([
                                Repeater::make('editable_data.content_seminar.bibliography_seminar')
                                    ->label('References')
                                    ->schema([
                                        Textarea::make('citation')
                                            ->label('Citare bibliografică')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă referință'),
                            ]),
                    ])
                    ->hidden(fn(?CourseSyllabusContent $record) =>
                        !$record || !$record->courseAssignment || ($record->courseAssignment->curriculumCourse->seminar_hours ?? 0) == 0
                    ),

                // Step 7.2: Project Content (Conditional)
                Wizard\Step::make('content_project')
                    ->label('Project Content')
                    ->schema([
                        Section::make('8.4. Proiect - Conținut')
                            ->schema([
                                Repeater::make('editable_data.content_project.project_content')
                                    ->label('Project Content')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Conținut')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpan(2),
                                        TextInput::make('hours')
                                            ->label('Ore')
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă conținut'),
                            ]),

                        Section::make('8.4. Proiect - Metode de predare')
                            ->schema([
                                Textarea::make('editable_data.content_project.project_teaching_methods')
                                    ->label('Metode de predare')
                                    ->rows(3),
                            ]),

                        Section::make('8.4. Proiect - Bibliografie')
                            ->schema([
                                Repeater::make('editable_data.content_project.bibliography_project')
                                    ->label('References')
                                    ->schema([
                                        Textarea::make('citation')
                                            ->label('Citare bibliografică')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă referință'),
                            ]),
                    ])
                    ->hidden(fn(?CourseSyllabusContent $record) =>
                        !$record || !$record->courseAssignment || ($record->courseAssignment->curriculumCourse->project_hours ?? 0) == 0
                    ),

                // Step 7.3: Practice Content (Conditional)
                Wizard\Step::make('content_practice')
                    ->label('Practice Content')
                    ->schema([
                        Section::make('8.5. Practică - Conținut')
                            ->schema([
                                Repeater::make('editable_data.content_practice.practice_content')
                                    ->label('Practice Content')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Conținut')
                                            ->required()
                                            ->rows(2)
                                            ->columnSpan(2),
                                        TextInput::make('hours')
                                            ->label('Ore')
                                            ->required()
                                            ->numeric(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă conținut'),
                            ]),

                        Section::make('8.5. Practică - Metode de predare')
                            ->schema([
                                Textarea::make('editable_data.content_practice.practice_teaching_methods')
                                    ->label('Metode de predare')
                                    ->rows(3),
                            ]),

                        Section::make('8.5. Practică - Bibliografie')
                            ->schema([
                                Repeater::make('editable_data.content_practice.bibliography_practice')
                                    ->label('References')
                                    ->schema([
                                        Textarea::make('citation')
                                            ->label('Citare bibliografică')
                                            ->required()
                                            ->rows(2),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Adaugă referință'),
                            ]),
                    ])
                    ->hidden(fn(?CourseSyllabusContent $record) =>
                        !$record || !$record->courseAssignment || ($record->courseAssignment->curriculumCourse->practice_hours ?? 0) == 0
                    ),

                // Step 8: Alignment
                Wizard\Step::make('alignment')
                    ->label('Alignment')
                    ->schema([
                        Textarea::make('editable_data.alignment.alignment_content')
                            ->label('9. Coroborarea conținuturilor disciplinei cu așteptările reprezentanților comunității epistemice')
                            ->required()
                            ->rows(5),
                    ]),

                // Step 9: Evaluation
                Wizard\Step::make('evaluation')
                    ->label('Evaluation')
                    ->schema([
                        Textarea::make('editable_data.evaluation.evaluation_conditions')
                            ->label('10.A. Condiții de îndeplinit pentru prezentarea la evaluare')
                            ->rows(3),

                        Section::make('10.B. Criterii, metode și ponderi')
                            ->schema([
                                // Curs - Sor 1
                                Section::make('Curs - Evaluare 1')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.course_evaluation_criteria_1')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.course_evaluation_methods_1')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.course_evaluation_weight_1')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),

                                // Curs - Sor 2
                                Section::make('Curs - Evaluare 2')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.course_evaluation_criteria_2')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.course_evaluation_methods_2')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.course_evaluation_weight_2')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),

                                // Seminar
                                Section::make('Seminar')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.seminar_evaluation_criteria')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.seminar_evaluation_methods')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.seminar_evaluation_weight')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),

                                // Laborator
                                Section::make('Laborator')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.laboratory_evaluation_criteria')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.laboratory_evaluation_methods')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.laboratory_evaluation_weight')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),

                                // Proiect
                                Section::make('Proiect')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.project_evaluation_criteria')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.project_evaluation_methods')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.project_evaluation_weight')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),

                                // Practică
                                Section::make('Practică')
                                    ->schema([
                                        Textarea::make('editable_data.evaluation.practice_evaluation_criteria')
                                            ->label('Criterii de evaluare')
                                            ->rows(2),
                                        Textarea::make('editable_data.evaluation.practice_evaluation_methods')
                                            ->label('Metode de evaluare')
                                            ->rows(2),
                                        TextInput::make('editable_data.evaluation.practice_evaluation_weight')
                                            ->label('Pondere')
                                            ->suffix('%'),
                                    ])->columns(3),
                            ]),

                        Textarea::make('editable_data.evaluation.minimum_performance_standards')
                            ->label('10.6. Standard minim de performanță')
                            ->required()
                            ->rows(3),
                    ]),

                // Step 10: Preview & Finalize
                // Pasul 10: Previzualizare și Finalizare
                Wizard\Step::make('preview')
                    ->label('Previzualizare și Finalizare')
                    ->schema([
                        Placeholder::make('preview_info')
                            ->label('Informații importante')
                            ->content('Vă rugăm să verificați conținutul fișei disciplinei înainte de finalizare. Odată blocată, orice modificare ulterioară va necesita aprobarea administratorului.'),

                        DatePicker::make('editable_data.signatures.appr_date')
                            ->label('Data avizării în departament')
                            ->default(now()),

                        Toggle::make('is_locked')
                            ->label('Finalizează și blochează fișa disciplinei')
                            ->helperText('După finalizare, nu mai puteți edita conținutul fără aprobarea unui administrator'),
                    ]),
            ])
            ->persistStepInQueryString()
            ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('courseAssignment.curriculumCourse.course_name_ro')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'completed',
                    ])
                    ->sortable(),

                TextColumn::make('version')
                    ->label('Version')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('program_id')
                    ->label('Szak (Program)')
                    ->relationship('courseAssignment.curriculumCourse.curriculum.program', 'name_hu')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseSyllabusContents::route('/'),
            'create' => CreateCourseSyllabusContent::route('/create'),
            'edit' => EditCourseSyllabusContent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole('teacher')) {
            $teacher = $user->teacher; // Assuming User has a 'teacher' relation

            if ($teacher) {
                $query->whereHas('courseAssignment', function ($q) use ($teacher) {
                    $q->where('course_leader_id', $teacher->id)
                      ->orWhere('seminar_leader_id', $teacher->id)
                      ->orWhere('lab_leader_id', $teacher->id)
                      ->orWhere('project_leader_id', $teacher->id);
                });
            } else {
                // If user is teacher but has no teacher record, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }
}
