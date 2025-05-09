<?php

namespace App\Enums;

enum DocumentTypesEnum: string
{
    case NIT = 'NIT';

    public function id(): int
    {
        return match ($this) {
            self::NIT => 1,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::NIT => 'Número de Identificación Tributaria',
        };
    }
}
