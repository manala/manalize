<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Requirement\Common;

trait RequirementLevelHolderTrait
{
    /**
     * Is the requirement mandatory ("required") or recommended ?
     *
     * @var int
     */
    private $level;

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getLevelLabel()
    {
        return RequirementLevel::getLabels()[$this->level];
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->level === RequirementLevel::REQUIRED;
    }
}
