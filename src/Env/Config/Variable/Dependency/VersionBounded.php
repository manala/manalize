<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Env\Config\Variable\Dependency;

use Composer\Semver\Semver;

/**
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class VersionBounded extends Dependency
{
    /**
     * @var string
     */
    private $version;

    /**
     * {@inheritdoc}
     *
     * @param string $version
     */
    public function __construct($name, $enabled, $version)
    {
        parent::__construct($name, $enabled);

        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces()
    {
        return [
            sprintf('%s_version', $this->getName()) => $this->getVersion(),
        ] + parent::getReplaces();
    }

    /**
     * Checks that the required dependency version matches the supported constraint.
     *
     * @param string|null $version
     * @param string|null $constraint
     */
    public static function validate($version = null, $constraint = null)
    {
        if (false === Semver::satisfies($version, $constraint)) {
            throw new \InvalidArgumentException(sprintf(
                'Version "%s" doesn\'t match constraint "%s"',
                $version,
                $constraint
            ));
        }

        return $version;
    }
}
