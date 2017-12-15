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
 * The "tld" var.
 *
 * @author Maxime STEINHAUSSER <maxime.steinhausser@gmail.com>
 */
final class Tld extends SingleValue
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'tld';
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(string $value)
    {
        if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
            throw new \InvalidArgumentException(sprintf(
                'This value must contain only alphanumeric characters and hyphens.',
                $value
            ));
        }

        return strtolower($value);
    }
}
