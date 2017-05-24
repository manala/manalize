<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Manifest;

use Manala\Manalize\Env\TemplateName;
use Symfony\Component\Yaml\Parser;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class ManifestParser
{
    const DEFAULTS_PATH = MANALIZE_HOME.'/templates/%s/manifest.yaml';

    public static function parse(TemplateName $envName): Manifest
    {
        $raw = (new Parser())->parse(file_get_contents(sprintf(self::DEFAULTS_PATH, $envName->getValue())));

        return new Manifest($raw);
    }
}
