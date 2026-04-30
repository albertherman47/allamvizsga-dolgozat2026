<?php

namespace App\Filament\Forms\Components;

use Filament\Schemas\Components\Component;

/**
 * Egyedi Filament űrlap-komponens a tantárgy-adatlap táblázatos megjelenítéséhez.
 *
 * A Blade nézete: resources/views/filament/forms/components/syllabus-table-form.blade.php
 * Befőzési pont, ha a tantárgy-adatlapot táblázatos formában kell megjeleníteni
 * (pl. PDF-szerű előnézethez vagy szerkesztőben).
 */
class SyllabusTableForm extends Component
{
    protected string $view = 'filament.forms.components.syllabus-table-form';
}
