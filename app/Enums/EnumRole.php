<?php


namespace App\Enums;

class EnumRole
{
    public const CLIENT = 'CLIENT';
    public const ADMIN = 'ADMIN';
    public const BOUTIQUIER = 'BOUTIQUIER';

    /**
     * Retourne toutes les valeurs de l'énumération.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return [
            self::CLIENT,
            self::ADMIN,
            self::BOUTIQUIER,
        ];
    }
}
