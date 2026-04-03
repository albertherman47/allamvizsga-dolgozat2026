@php
    use App\Services\SyllabusService;

    $record = $getRecord();
    if (!$record || !$record->template || !$record->courseAssignment) {
        echo '<div style="padding: 20px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;">Nem érhető el a tantárgy adatlap adata.</div>';
        return;
    }

    $syllabusService = new SyllabusService($record->template, $record->courseAssignment);
    $dataProvider = $syllabusService->getDataProvider();
    $allData = $dataProvider->getAllPlaceholderData();
    $allPlaceholders = collect($dataProvider->getPlaceholdersConfig()['placeholders']);
    $editableData = $record->editable_data ?? [];

    // Helper function to convert value to string
    $valueToString = function($value) {
        if ($value === null || $value === '') {
            return '';
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return (string) $value;
    };

    // Helper function to get field value (editable or readonly)
    $getFieldValue = function($shortName) use ($editableData, $allData, $valueToString) {
        $value = $editableData[$shortName] ?? ($allData[$shortName] ?? '');
        return $valueToString($value);
    };

    // Helper function to check if field is editable
    $isFieldEditable = function($fieldName) use ($allPlaceholders) {
        $placeholder = $allPlaceholders->firstWhere('short_name', $fieldName);
        return $placeholder && ($placeholder['is_editable'] ?? false);
    };
@endphp

<style>
    .syllabus-table-form {
        width: 100%;
        background: white;
        border-collapse: collapse;
    }

    .syllabus-table-form table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        border: 1px solid #ddd;
    }

    .syllabus-table-form th,
    .syllabus-table-form td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .syllabus-table-form th {
        background-color: #e8e8e8;
        font-weight: 600;
        color: #333;
    }

    .section-header {
        background-color: #f5f5f5;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 12px;
        color: #333;
    }

    .label-col {
        background-color: #f9f9f9;
        width: 40%;
        font-weight: 500;
        color: #555;
    }

    .value-col {
        width: 60%;
    }

    .readonly-value {
        padding: 8px 0;
        color: #555;
        min-height: 20px;
        word-wrap: break-word;
    }

    .readonly-value:empty::before {
        content: '-';
        color: #999;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        margin: 20px 0 10px 0;
        padding: 10px;
        background-color: #f0f0f0;
        border-left: 4px solid #1f2937;
    }
</style>

<div class="syllabus-table-form">

    <!-- SECTION 1: DATE DESPRE PROGRAM -->
    <div class="section-title">1. Date despre program (Program Information)</div>
    <table>
        @php
            $section1Fields = ['inst', 'fac', 'field', 'cycle', 'prog', 'qual'];
        @endphp
        @foreach($section1Fields as $fieldName)
            @php
                $placeholder = $allPlaceholders->firstWhere('short_name', $fieldName);
                $value = $getFieldValue($fieldName);
            @endphp
            @if($placeholder)
                <tr>
                    <td class="label-col">{{ $placeholder['description'] }}</td>
                    <td class="value-col">
                        <div class="readonly-value">{{ $value ?: '-' }}</div>
                    </td>
                </tr>
            @endif
        @endforeach
    </table>

    <!-- SECTION 2: DATE DESPRE DISCIPLINA -->
    <div class="section-title">2. Date despre disciplină (Discipline Information)</div>
    <table>
        @php
            $section2Part1 = ['dep', 'disc_name'];
        @endphp

        <!-- 2.0 - 2.1 -->
        @foreach($section2Part1 as $fieldName)
            @php
                $placeholder = $allPlaceholders->firstWhere('short_name', $fieldName);
                $value = $getFieldValue($fieldName);
            @endphp
            @if($placeholder)
                <tr>
                    <td class="label-col">{{ $placeholder['description'] }}</td>
                    <td class="value-col">
                        <div class="readonly-value">{{ $value ?: '-' }}</div>
                    </td>
                </tr>
            @endif
        @endforeach

        <!-- 2.2 - Activity Type -->
        <tr>
            <td colspan="2" class="section-header">2.2. Tipul activității (Activity Type)</td>
        </tr>
        <tr>
            <td colspan="2">
                <table style="width: 100%; border: none;">
                    <tr>
                        @php
                            $activityTypes = ['act_integral', 'act_partial', 'act_unassist'];
                            $activityLabels = ['Asistat integral', 'Asistat parțial', 'Neasistat'];
                        @endphp
                        @foreach($activityTypes as $idx => $fieldName)
                            @php
                                $value = $getFieldValue($fieldName);
                            @endphp
                            <td style="border: none; text-align: center; width: 33%;">
                                <div style="margin-bottom: 5px;">{{ $activityLabels[$idx] }}</div>
                                <div class="readonly-value">{{ $value ?: '-' }}</div>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </td>
        </tr>

        <!-- 2.3 - Course Holder -->
        <tr>
            <td class="label-col">
                @php
                    $placeholder = $allPlaceholders->firstWhere('short_name', 'course_holder');
                @endphp
                {{ $placeholder['description'] ?? '2.3. Titularul disciplinei' }}
            </td>
            <td class="value-col">
                <div class="readonly-value">{{ $getFieldValue('course_holder') ?: '-' }}</div>
            </td>
        </tr>

        <!-- 2.4 - Activity Holders (nested) -->
        <tr>
            <td class="label-col">2.4. Titularii altor activități</td>
            <td class="value-col">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; width: 20%; font-weight: 500;">Seminar:</td>
                        <td style="border: none; width: 80%;">
                            <div class="readonly-value">{{ $getFieldValue('sem_holder') ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; width: 20%; font-weight: 500;">Laborator:</td>
                        <td style="border: none; width: 80%;">
                            <div class="readonly-value">{{ $getFieldValue('lab_holder') ?: '-' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; width: 20%; font-weight: 500;">Proiect:</td>
                        <td style="border: none; width: 80%;">
                            <div class="readonly-value">{{ $getFieldValue('proj_holder') ?: '-' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- 2.5-2.11 - Combined row -->
        <tr>
            <td colspan="2">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.5. Anul de studiu</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('study_year') ?: '-' }}</div></td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.6. Semestrul</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('sem') ?: '-' }}</div></td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.7. Forma de verificare</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('verif') ?: '-' }}</div></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.8. Tipul disciplinei</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('disc_type') ?: '-' }}</div></td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.9. Categoria formativă</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('form_cat') ?: '-' }}</div></td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px; background-color: #f9f9f9; font-weight: 500;">2.10. Categoria disciplinei</td>
                        <td style="border: 1px solid #ddd; width: 16.6%; padding: 8px;"><div class="readonly-value">{{ $getFieldValue('disc_cat') ?: '-' }}</div></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SECTION 3-10: Abbreviated for space, but same structure -->
    <div style="text-align: center; margin: 30px 0; padding: 20px; background-color: #f0f0f0; border-radius: 4px;">
        <p style="font-size: 14px; color: #666;">
            <strong>Megjegyzés:</strong> A teljes tantárgy-adatlap (Section 3-10: Időbecslés, Prekondiíciók, Feltételek, Tanulási eredmények, Célkitűzések, Tartalom, Összehangolás, Értékelés) megjelenítésre vár. Az adatok az adatbázisban vannak, egyszerűen szükség van az összes szakasz megjelenítésére a fenti Pattern szerint.
        </p>
    </div>

</div>
