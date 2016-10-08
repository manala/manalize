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

/**
 * Iterable collection of requirement violations.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class RequirementViolationList extends \ArrayObject
{
    /**
     * {@inheritdoc}
     *
     * @param RequirementViolation[] $violations
     */
    public function __construct(array $violations = [])
    {
        parent::__construct();

        foreach ($violations as $violation) {
            $this->addViolation($violation);
        }
    }

    public function addViolation(RequirementViolation $violation)
    {
        $this->append($violation);
    }

    /**
     * @return RequirementViolation[]
     */
    public function getViolations()
    {
        return $this->getArrayCopy();
    }

    /**
     * Does the violation list contain at least one violation with the given level ?
     *
     * @param string $level
     *
     * @return bool
     */
    private function containsViolations($level)
    {
        foreach ($this->getViolations() as $violation) {
            if ($violation->getLevel() === $level) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function containsRequiredViolations()
    {
        return $this->containsViolations(RequirementLevel::REQUIRED);
    }

    /**
     * @return bool
     */
    public function containsRecommendedViolations()
    {
        return $this->containsViolations(RequirementLevel::RECOMMENDED);
    }
}
