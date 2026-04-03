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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle finalize/lock logic
        if (isset($data['is_locked']) && $data['is_locked'] === true) {
            $data['status'] = 'completed';
            $data['completed_at'] = now();

            // Generate snapshot of all placeholder values
            $template = SyllabusTemplate::find($this->record->template_id);
            $resolver = new PlaceholderResolver($this->record, $this->record->courseAssignment);

            $snapshot = [];

            foreach ($template->placeholders_config['sections'] as $section) {
                foreach ($section['placeholders'] as $placeholder) {
                    $value = $resolver->resolve($placeholder);
                    $snapshot[$placeholder['name']] = $resolver->formatOutput($value, $placeholder);
                }
            }

            $data['completed_snapshot'] = $snapshot;
        }

        return $data;
    }
}
