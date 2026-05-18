<?php

namespace App\Filament\Resources\Curricula\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurriculaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('program.name_hu') 
                    ->label('Szak')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.year_code')
                    ->label('Tanév')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Tanterv neve')
                    ->searchable(),
                TextColumn::make('hours_per_credit')
                    ->label('Óra / Kredit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('clone')
                    ->label('Másolás')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\Select::make('program_id')
                            ->label('Cél Szak')
                            ->options(\App\Models\Program::pluck('name_hu', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('academic_year_id')
                            ->label('Cél Tanév')
                            ->options(\App\Models\AcademicYear::pluck('year_code', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Új Tanterv neve')
                            ->required(),
                    ])
                    ->action(function (\App\Models\Curriculum $record, array $data) {
                        $newCurriculum = $record->replicate();
                        $newCurriculum->program_id = $data['program_id'];
                        $newCurriculum->academic_year_id = $data['academic_year_id'];
                        $newCurriculum->name = $data['name'];
                        $newCurriculum->save();

                        foreach ($record->courses as $course) {
                            $newCourse = $course->replicate();
                            $newCourse->curriculum_id = $newCurriculum->id;
                            $newCourse->save();
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Tanterv sikeresen lemásolva')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
