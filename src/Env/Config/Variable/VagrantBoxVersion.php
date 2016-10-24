<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable;

/**
 * The manala/app-dev-debian box version.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class VagrantBoxVersion extends SingleValue
{
    protected static $placeholder = '{{ box_version }}';

    /**
     * {@inheritdoc}
     */
    public static function validate($version)
    {
        if (!in_array($version, self::getSupportedVersions(), true)) {
            throw new \InvalidArgumentException(sprintf(
                'The "%s" version doesn\'t exist or is not supported.',
                $version
            ));
        }

        return $version;
    }

    private static function getSupportedVersions()
    {
        return [
            '~> 2.0.0',
            '~> 3.0.0',
        ];
    }
}
