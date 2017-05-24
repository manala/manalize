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
 * @method static TemplateName ELAO_SYMFONY()
 * @method static TemplateName CUSTOM()
 */
final class TemplateName extends ReadableEnum
{
    const ELAO_SYMFONY = 'elao-symfony';
    const CUSTOM = 'custom';

    public static function values(): array
    {
        return [
            self::ELAO_SYMFONY,
            self::CUSTOM,
        ];
    }

    public static function readables(): array
    {
        return [
            self::ELAO_SYMFONY => 'Elao Symfony',
            self::CUSTOM => ucfirst(self::CUSTOM),
        ];
    }
}
