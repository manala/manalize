<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Requirement\Processor;

class BinaryProcessor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    public function getCommand($name)
    {
        return sprintf('%s --version', $name);
    }
}
