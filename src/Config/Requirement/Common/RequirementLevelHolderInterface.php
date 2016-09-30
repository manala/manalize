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

/**
 * Interface to be implemented by requirement level holders (Eg Requirement, Violation).
 */
interface RequirementLevelHolderInterface
{
    /**
     * @see RequirementLevel
     *
     * @return int
     */
    public function getLevel();

    /**
     * Human readable label for requirement level. Eg "Required", "Recommended".
     *
     * @return string
     */
    public function getLevelLabel();

    /**
     * Is the requirement mandatory ('Required') or just recommended ?
     *
     * @return bool
     */
    public function isRequired();
}
