<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Config\Requirement\Common;

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
        switch ($this->level) {
            case RequirementLevel::RECOMMENDED:
                return 'Recommended';
            case RequirementLevel::REQUIRED:
                return 'Required';
        }
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->level === RequirementLevel::REQUIRED;
    }
}
