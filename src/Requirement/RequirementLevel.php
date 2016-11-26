<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement;

use Elao\Enum\ReadableEnum;

/**
 * @method static RequirementLevel REQUIRED()
 * @method static RequirementLevel RECOMMENDED()
 */
final class RequirementLevel extends ReadableEnum
{
    const REQUIRED = 'required';
    const RECOMMENDED = 'recommended';

    public static function values(): array
    {
        return [
            self::REQUIRED,
            self::RECOMMENDED,
        ];
    }

    public static function readables(): array
    {
        return [
            self::RECOMMENDED => 'Recommended',
            self::REQUIRED => 'Required',
        ];
    }
}
