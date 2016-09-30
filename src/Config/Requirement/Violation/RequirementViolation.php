<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Violation;

use Manala\Config\Requirement\Common\RequirementLevel;
use Manala\Config\Requirement\Common\RequirementLevelHolderInterface;
use Manala\Config\Requirement\Common\RequirementLevelHolderTrait;

/**
 * Class that represents a requirement violation. It contains the name of the required binary (Ansible, vagrant, etc.),
 * the violation's label (missing binary or insufficient version), the violation level (required|recommended) and an
 * optional help.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class RequirementViolation implements RequirementLevelHolderInterface
{
    use RequirementLevelHolderTrait;

    /** @var string */
    private $name;

    /** @var string */
    private $label;

    /** @var string */
    private $help;

    /**
     * @param string      $name
     * @param string      $label
     * @param int         $level
     * @param string|null $help
     */
    public function __construct($name, $label, $level = RequirementLevel::REQUIRED, $help = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->level = $level;
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }
}
