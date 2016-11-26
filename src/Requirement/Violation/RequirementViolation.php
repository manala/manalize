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

use Manala\Manalize\Requirement\Requirement;

/**
 * Class that represents a requirement violation. It contains the name of the required binary (Ansible, vagrant, etc.),
 * the violation's label (missing binary or insufficient version), the violation level (required|recommended) and an
 * optional help.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
final class RequirementViolation
{
    private $violatedRequirement;
    private $label;

    public function __construct(Requirement $violatedRequirement, string $label)
    {
        $this->violatedRequirement = $violatedRequirement;
        $this->label = $label;
    }

    public function getViolatedRequirement(): Requirement
    {
        return $this->violatedRequirement;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
