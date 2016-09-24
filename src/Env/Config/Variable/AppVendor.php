<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config\Variable;

/**
 * Manala "app" and "vendor" environment variables.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class AppVendor implements Variable
{
    private static $vendorPlaceholder = '{{ vendor }}';
    private static $appPlaceholder = '{{ app }}';

    /**
     * @param string $vendor
     * @param string $app
     */
    public function __construct($vendor, $app = 'app')
    {
        $this->vendor = strtolower($vendor);
        $this->app = strtolower($app);
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces()
    {
        return [
            self::$vendorPlaceholder => $this->vendor,
            self::$appPlaceholder    => $this->app,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function validate($value = null)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf(
                'This value must contain only alphanumeric characters and hyphens.',
                $value
            ));
        }

        return $value;
    }
}
