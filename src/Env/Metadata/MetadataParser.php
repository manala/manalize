<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Metadata;

use Manala\Manalize\Env\EnvEnum;
use Symfony\Component\Yaml\Parser;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class MetadataParser
{
    const METADATA_PATH = MANALIZE_DIR.'/src/Resources/%s/manala.yml';

    public static function parse(EnvEnum $envName) : MetadataBag
    {
        $raw = (new Parser())->parse(file_get_contents(sprintf(self::METADATA_PATH, $envName)));

        return new MetadataBag($raw);
    }
}
