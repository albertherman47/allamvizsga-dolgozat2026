<?php

namespace App\Filament\Resources\Curricula\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $recordTitleAttribute = 'course_name_hu';

    protected static ?string $title = 'Tantárgyak';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('course_code')
                    ->label('Kód')
                    ->required()
                    ->maxLength(255),
                TextInput::make('course_name_hu')
                    ->label('Magyar név')
                    ->required()
                    ->maxLength(255),
                TextInput::make('study_year')
                    ->label('Évfolyam')
                    ->required()
                    ->numeric(),
                TextInput::make('semester')
                    ->label('Félév')
                    ->required()
                    ->numeric(),
                TextInput::make('credits')
                    ->label('Kredit')
                    ->required()
                    ->numeric(),
                TextInput::make('lecture_hours')
                    ->label('Előadás óra')
                    ->required()
                    ->numeric(),
                TextInput::make('seminar_hours')
                    ->label('Szeminárium óra')
                    ->required()
                    ->numeric(),
                TextInput::make('lab_hours')
                    ->label('Labor óra')
                    ->required()
                    ->numeric(),
                TextInput::make('project_hours')
                    ->label('Projekt óra')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_code')->label('Kód')->searchable(),
                TextColumn::make('course_name_hu')->label('Név')->searchable(),
                TextColumn::make('study_year')->label('Évfolyam')->sortable(),
                TextColumn::make('semester')->label('Félév')->sortable(),
                TextColumn::make('credits')->label('Kredit')->sortable(),
                TextColumn::make('lecture_hours')->label('Előadás')->sortable(),
                TextColumn::make('seminar_hours')->label('Szeminárium')->sortable(),
                TextColumn::make('lab_hours')->label('Labor')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
}
