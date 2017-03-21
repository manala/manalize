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

use Composer\Semver\Semver;

/**
 * Representation of a package to be installed during the VM provisioning.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Package extends Variable
{
    private $version;
    private $enabled;

    public function __construct(string $name, bool $enabled, string $version = null)
    {
        parent::__construct($name, ['enabled' => $enabled, 'version' => $version]);

        $this->version = $version;
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getReplaces(): array
    {
        $name = $this->getName();
        $replaces = [];

        if ($this->version) {
            $replaces["{$name}_version"] = $this->version;
        }

        $replaces["{$name}_enabled"] = $this->isEnabled() ? 'true' : 'false';

        return $replaces;
    }

    /**
     * @param string $version
     * @param string $constraint
     *
     * @return string The version if it satisfies the constraint
     *
     * @throws \InvalidArgumentException If the version does not satisfy the constraint
     */
    public static function validate($version, $constraint = null)
    {
        $match = false;
        $e = null;

        try {
            $match = Semver::satisfies($version, $constraint);
        } catch (\UnexpectedValueException $e) {
        }

        if (false === $match || null !== $e) {
            throw new \InvalidArgumentException(sprintf(
                'Version "%s" doesn\'t match constraint "%s"',
                $version,
                $constraint
            ));
        }

        return $version;
    }
}
