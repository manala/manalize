<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Violation;

use Manala\Manalize\Requirement\Common\RequirementLevel;
use Manala\Manalize\Requirement\Common\RequirementLevelHolderInterface;
use Manala\Manalize\Requirement\Common\RequirementLevelHolderTrait;

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

    private $name;
    private $label;
    private $help;

    public function __construct(string $name, string $label, string $level = RequirementLevel::REQUIRED, $help = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->level = $level;
        $this->help = $help;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @return string|null
     */
    public function getHelp()
    {
        return $this->help;
    }
}
