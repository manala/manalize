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
    protected $name;
    protected $enabled;

    /**
     * @param string $name
     * @param bool   $enabled
     */
    public function __construct(string $name, bool $enabled)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces(): array
    {
        return [
            sprintf('{{ %s_enabled }}', $this->getName()) => $this->isEnabled() ? 'true' : 'false',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(string $value)
    {
        return $value;
    }
}
