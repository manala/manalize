<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Processor;

class BinaryProcessor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    public function getCommand(string $name): string
    {
        return sprintf('%s --version', $name);
    }
}
