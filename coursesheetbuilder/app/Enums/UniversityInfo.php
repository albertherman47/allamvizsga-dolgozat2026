<?php

namespace App\Enums;

enum UniversityInfo: string
{
    case INSTITUTION_NAME = 'institution_name';
    case FACULTY_NAME = 'faculty_name';

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

    public static function all(string $lang = 'ro'): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->get($lang);
        }
        return $result;
    }
}
