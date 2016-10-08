<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Common;

final class RequirementLevel
{
    const REQUIRED = 1;
    const RECOMMENDED = 2;

    public static function getLabels()
    {
        return [
            self::RECOMMENDED => 'Recommended',
            self::REQUIRED => 'Required',
        ];
    }
}
