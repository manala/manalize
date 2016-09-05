<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Config;

/**
 * Config dumper.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Dumper
{
    private static $vendorKey = '{{ vendor }}';
    private static $appKey = '{{ app }}';
    private static $databaseKey = '{{ app_database }}';
    private static $hostKey = '{{ app_host }}';

    /**
     * Dumps a rendered Config.
     *
     * @param Config $config The whole Config for which to dump the template
     * @param Vars   $vars   The vars to insert
     */
    public static function dump(Config $config, Vars $vars)
    {
        $template = $config->getTemplate();

        if (!is_writable($template) || !is_readable($template)) {
            throw new \RuntimeException('The origin file is either not readable, not writable or it doesn\'t exist.');
        }

        $replaces = [
            self::$vendorKey => $vars->getVendor(),
            self::$appKey => $vars->getApp(),
            self::$databaseKey => $vars->getDatabase(),
            self::$hostKey => $vars->getHost(),
        ];

        return strtr(file_get_contents($template), $replaces);
    }
}
