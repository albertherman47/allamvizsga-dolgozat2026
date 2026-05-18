<?php

namespace App\Filament\Resources\Syllabi;

use App\Filament\Resources\Syllabi\Pages\CreateSyllabusTemplate;
use App\Filament\Resources\Syllabi\Pages\EditSyllabusTemplate;
use App\Filament\Resources\Syllabi\Pages\ListSyllabusTemplates;
use App\Models\SyllabusTemplate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SyllabusTemplateResource extends Resource
{
    protected static ?string $model = SyllabusTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Tantárgyi adatlap sablonok';

    protected static \UnitEnum|string|null $navigationGroup = 'Tantárgyi adatlap';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->hasRole('admin') || (!$user->isTeacher() && !$user->isAdministrativeStaff());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Sablon neve')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g., Fișa disciplinei 2025/26'),

                Select::make('academic_year_id')
                    ->label('Academic Year')
                    ->relationship('academicYear', 'year_code')
                    ->required()
                    ->searchable(),

                TextInput::make('docx_template_path')
                    ->label('DOCX Template Path')
                    ->maxLength(255)
                    ->placeholder('storage/templates/fisa_2025_26.docx')
                    ->helperText('Path to the DOCX template file'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('academicYear.year_code')
                    ->label('Academic Year')
                    ->sortable(),

                TextColumn::make('docx_template_path')
                    ->label('Template File')
                    ->limit(40),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => ListSyllabusTemplates::route('/'),
            'create' => CreateSyllabusTemplate::route('/create'),
            'edit' => EditSyllabusTemplate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
