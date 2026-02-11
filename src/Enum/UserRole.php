<?php

namespace App\Enum;

final class UserRole
{
    public const ADMIN = 'ROLE_ADMIN';
    public const ENOLOGO = 'ROLE_ENOLOGO';
    public const OPERADOR = 'ROLE_OPERADOR';
    public const LECTURA = 'ROLE_LECTURA';

    public const ALL = [
        self::ADMIN,
        self::ENOLOGO,
        self::OPERADOR,
        self::LECTURA
    ];
}
