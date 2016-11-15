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

class RequirementViolationLabelBuilder
{
    /**
     * Generates a violation label based on the requirement and current version.
     *
     * @param Requirement $requirement
     * @param string|null $currentVersion Null if expected executable is not installed on host
     *
     * @return string
     */
    public function buildViolationLabel(Requirement $requirement, $currentVersion = null): string
    {
        $isRequired = $requirement->isRequired();
        $label = $requirement->getLabel();

        if (null === $currentVersion) {
            return sprintf(
                $isRequired ? '%s is missing. Please install it before proceeding.' : 'You should consider installing %s.',
                $label
            );
        }

        $constraint = $requirement->getSemanticVersion();
        $conflicts = $requirement->getConflicts();

        return sprintf(
            $isRequired ?
                "Your %s version (%s) doesn't allow you to use this.\nPlease consider changing for a version satisfying constraint %s%s." :
                'Your %s version is %s. We recommend you to change for a version satifiying constraint %s%s.',
            $label,
            $currentVersion,
            $constraint,
            $conflicts ? sprintf(' (conflictig versions: %s)', implode(', ', $conflicts)) : ''
        );
    }
}
