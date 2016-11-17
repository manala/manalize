<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env;

use Elao\Enum\ReadableEnum;

/**
 * @method static EnvName SYMFONY()
 */
final class EnvName extends ReadableEnum
{
    const SYMFONY = 'symfony';

    public static function values(): array
    {
        return [
            self::SYMFONY,
        ];
    }

    public static function readables(): array
    {
        return [
            self::SYMFONY => ucfirst(self::SYMFONY),
        ];
    }
}
