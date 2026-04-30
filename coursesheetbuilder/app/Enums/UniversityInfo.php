<?php

namespace App\Enums;

enum UniversityInfo: string
{
    case INSTITUTION_NAME = 'institution_name';
    case FACULTY_NAME = 'faculty_name';

    /**
     * Provides localized labels for different entity types.
     *
     * This function returns an array of language-specific labels
     * for predefined constants, such as `INSTITUTION_NAME` and
     * `FACULTY_NAME`. Each constant maps to an associative array
     * containing translations in Romanian (`ro`), Hungarian (`hu`),
     * and English (`en`).
     *
     * @return array Localized labels for the entity types.
     */
    public function labels(): array
    {
        return match($this) {
            self::INSTITUTION_NAME => [
                'ro' => 'Universitatea Sapientia din Cluj-Napoca',
                'hu' => 'Sapientia Erdélyi Magyar Tudományegyetem',
                'en' => 'Sapientia Hungarian University of Transylvania',
            ],
            self::FACULTY_NAME => [
                'ro' => 'Științe Economice, Socio-Umane și Inginerești din Miercurea Ciuc',
                'hu' => 'Gazdaság-, Társadalom- és Műszaki Tudományok Kar, Csíkszereda',
                'en' => 'Faculty of Economics, Socio-Human Sciences and Engineering, Miercurea Ciuc',
            ],
        };
    }

    public function get(string $lang = 'ro'): string
    {
        return $this->labels()[$lang] ?? $this->labels()['ro'];
    }

    /**
     * Retrieves an associative array of case values and their corresponding localized names.
     *
     * @param string $lang The language code used to get the localized name. Defaults to 'ro'.
     * @return array An array where the keys are case values and the values are their localized names.
     */
    public static function all(string $lang = 'ro'): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->get($lang);
        }
        return $result;
    }
}
