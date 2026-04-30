<?php

namespace App\Filament\Resources\Syllabi\Pages;

use App\Filament\Resources\Syllabi\CourseSyllabusContentResource;
use App\Models\SyllabusTemplate;
use App\Services\CourseSyllabusFormBuilder;
use App\Services\PlaceholderResolver;
use App\Services\SyllabusDocxGenerator;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EditCourseSyllabusContent extends EditRecord
{
    protected static string $resource = CourseSyllabusContentResource::class;

    /**
     * Defines the header actions for the current context.
     *
     * The actions include generating and downloading the syllabus
     * and deleting the corresponding record. In case of an error
     * during syllabus generation, an error notification is displayed.
     *
     * @return array List of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_syllabus')
                ->label('Download Syllabus')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    try {
                        $generator = new SyllabusDocxGenerator($this->record, $this->record->courseAssignment);
                        $filePath = $generator->generate();

                        Notification::make()
                            ->success()
                            ->title('Syllabus Generated')
                            ->body('Your syllabus has been generated successfully.')
                            ->send();

                        // Return file download
                        return response()->download($filePath, basename($filePath));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Error Generating Syllabus')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mutates the form data before saving it to handle the finalize or unlock logic.
     *
     * If the data includes a locked state (`is_locked` is true), the method marks the record
     * as completed, sets the completion timestamp, and generates a snapshot of placeholder
     * values based on the current form data and its related template configuration.
     *
     * If the data is not locked, the method reverts the record to draft status, clears the
     * completion timestamp, and removes the placeholder snapshot.
     *
     * @param array $data The form data to be processed before saving.
     * @return array The modified form data after applying the necessary logic.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle finalize/lock logic
        if (isset($data['is_locked']) && $data['is_locked'] === true) {
            $data['status'] = 'completed';
            $data['completed_at'] = now();

            // Generate snapshot of all placeholder values
            $template = SyllabusTemplate::find($this->record->template_id);

            // Create a temporary cloned record with the NEW data
            // This is essential so the PlaceholderResolver has the current edits!
            $tempRecord = clone $this->record;
            if (isset($data['editable_data'])) {
                $tempRecord->editable_data = $data['editable_data'];
            }
            $tempRecord->status = 'completed';

            $resolver = new PlaceholderResolver($tempRecord, $this->record->courseAssignment);

            $snapshot = [];

            if ($template && isset($template->placeholders_config['sections'])) {
                foreach ($template->placeholders_config['sections'] as $section) {
                    foreach ($section['placeholders'] as $placeholder) {
                        $value = $resolver->resolve($placeholder);
                        $snapshot[$placeholder['name']] = $resolver->formatOutput($value, $placeholder);
                    }
                }
            }

            $data['completed_snapshot'] = $snapshot;
        } else {
            // Unlock functionality: revert to draft and clear snapshot
            // so modifying the syllabus reflects correctly in the downloaded DOCX file.
            $data['status'] = 'draft';
            $data['completed_at'] = null;
            $data['completed_snapshot'] = null;
        }

        return $data;
    }
}
