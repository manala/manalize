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

use Manala\Config\Requirement\Requirement;

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
    public function buildViolationLabel(Requirement $requirement, $currentVersion = null)
    {
        $label = sprintf('[%s] ', strtoupper($requirement->getLevelLabel()));
        $isRequired = $requirement->isRequired();

        if ($currentVersion === null) {
            $label .= sprintf(
                $isRequired ?
                    '%s is missing. Please install it before proceeding.' :
                    'You should consider installing %s.',
                $requirement->getName()
            );
        } else {
            $label .= sprintf(
                $isRequired ?
                    "Your %s version %s doesn't allow you to use this. Required version is %s." :
                    'Your %s current version is %s. We recommend you to upgrade to version %s.',
                $requirement->getName(),
                $currentVersion,
                $requirement->getSemanticVersion()
            );
        }

        return $label;
    }
}
