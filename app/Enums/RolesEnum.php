<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMINISTRATOR = 'administrator';
    case ACCOUNT_EXECUTIVE = 'account-executive';

    public function id(): int
    {
        return match ($this) {
            self::ADMINISTRATOR => 1,
            self::ACCOUNT_EXECUTIVE => 2,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMINISTRATOR => 'Administradores',
            self::ACCOUNT_EXECUTIVE => 'Ejecutivos de Cuenta',
        };
    }
}
