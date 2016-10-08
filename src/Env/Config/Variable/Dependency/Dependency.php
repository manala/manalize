<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable\Dependency;

use Manala\Manalize\Env\Config\Variable\Variable;

/**
 * Representation of a dependency to be installed during the VM provisioning.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Dependency implements Variable
{
    private $name;
    private $enabled;

    /**
     * @param string $name
     * @param bool   $enabled
     */
    public function __construct($name, $enabled)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces()
    {
        return [
            sprintf('{{ %s_enabled }}', $this->getName()) => $this->isEnabled() ? 'true' : 'false',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function validate($value)
    {
    }
}
