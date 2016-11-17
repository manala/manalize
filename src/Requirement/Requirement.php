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

    private $label;
    private $name;
    private $semanticVersion;
    private $type;
    private $conflicts;
    private $help;

    /**
     * @param string      $label           The readable name
     * @param string      $name            The CLI utility name
     * @param string      $type
     * @param int         $level           One of 'recommended' and 'required'
     * @param string      $semanticVersion A semver constraint
     * @param array       $conflicts       An array of semver constraints corresponding to the conflicting versions of the utility
     * @param string|null $help            An help to resolve the requirement (i.e. a download link of the good version)
     */
    public function __construct(string $label, string $name, string $type, int $level, string $semanticVersion, array $conflicts = [], $help = null)
    {
        $this->label = $label;
        $this->name = $name;
        $this->type = $type;
        $this->level = $level;
        $this->semanticVersion = $semanticVersion;
        $this->conflicts = $conflicts;
        $this->help = $help;
    }

    /**
     * Gets the label of the required package/tool. Eg. Ansible, Vagrant, VirtualBox, Landrush, etc.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Gets the name of the required CLI utility e.g. ansible, vagrant, VboxManage, etc.
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
     * Gets the conflicting versions.
     *
     * @see https://getcomposer.org/doc/04-schema.md#conflict
     *
     * @return array An array of (semver compatible) versions
     */
    public function getConflicts(): array
    {
        return $this->conflicts;
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
