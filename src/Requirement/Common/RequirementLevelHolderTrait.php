<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Common;

trait RequirementLevelHolderTrait
{
    private $level;

    /**
     * {@inheritdoc}
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevelLabel(): string
    {
        return RequirementLevel::getLabels()[$this->level];
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired(): bool
    {
        return $this->level === RequirementLevel::REQUIRED;
    }
}
