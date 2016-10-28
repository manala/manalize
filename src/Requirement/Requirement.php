<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement;

/**
 * Class that represents a host's requirement: name of the required binary (eg. Ansible, vagrant, etc.), required version, etc.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class Requirement implements Common\RequirementLevelHolderInterface
{
    use Common\RequirementLevelHolderTrait;

    const TYPE_BINARY = 'binary';
    const TYPE_VAGRANT_PLUGIN = 'vagrant_plugin';

    private $name;
    private $semanticVersion;
    private $type;
    private $help;

    public function __construct(
        string $name,
        string $type,
        int $level,
        string $semanticVersion,
        $help = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->level = $level;
        $this->semanticVersion = $semanticVersion;
        $this->help = $help;
    }

    /**
     * Gets the name of the required package/tool. Eg. Ansible, vagrant, landrush etc.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets the requirement type.
     *
     * @return string One of {@link self::TYPE_BINARY} and {@link self::TYPE_VAGRANT_PLUGIN}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Gets the requirement semantic version.
     *
     * Example return: '^1.8.2', '~1.8', etc.
     *
     * @see https://getcomposer.org/doc/articles/versions.md
     *
     * @return string
     */
    public function getSemanticVersion(): string
    {
        return $this->semanticVersion;
    }

    /**
     * An optional help to display to the user if the executable is missing or if its version is inferior to the
     * required one. It can be for instance a link to the executable's download page.
     *
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }
}
