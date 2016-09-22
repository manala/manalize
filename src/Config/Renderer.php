<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config;

/**
 * Config' template renderer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Renderer
{
    private static $vendorKey = '{{ vendor }}';
    private static $appKey = '{{ app }}';

    /**
     * Renders a config template.
     *
     * @param Config $config The whole Config for which to dump the template
     * @param Vars   $vars   The vars to insert
     */
    public static function render(Config $config, Vars $vars)
    {
        $template = $config->getTemplate();

        if (!is_readable($template)) {
            throw new \RuntimeException(sprintf('The template file "%s" is either not readable or doesn\'t exist.', $template));
        }

        $replaces = [
            self::$vendorKey => $vars->getVendor(),
            self::$appKey => $vars->getApp(),
        ];

        return strtr(file_get_contents($template), $replaces);
    }
}
