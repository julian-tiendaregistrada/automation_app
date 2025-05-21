<?php

namespace App\Enums;

enum AreasEnum: string
{
    case COMMERCIAL = 'Comercial';
    case BI = 'BI';
    case HUMAN_RESOURCES = 'Recursos Humanos';

    public function id(): int
    {
        return match ($this) {
            self::COMMERCIAL => 1,
            self::BI => 2,
            self::HUMAN_RESOURCES => 3,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::COMMERCIAL => 'Área Comercial',
            self::BI => 'Business Intelligence',
            self::HUMAN_RESOURCES => 'Recursos Humanos',
        };
    }
}
