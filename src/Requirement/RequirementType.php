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

use Elao\Enum\Enum;

/**
 * @method static RequirementType BINARY()
 * @method static RequirementType VAGRANT_PLUGIN()
 */
final class RequirementType extends Enum
{
    const BINARY = 'binary';
    const VAGRANT_PLUGIN = 'vagrant_plugin';

    public static function values(): array
    {
        return [
            self::BINARY,
            self::VAGRANT_PLUGIN,
        ];
    }
}
