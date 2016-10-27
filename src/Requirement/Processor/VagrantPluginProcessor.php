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

use Manala\Manalize\Requirement\Exception\MissingRequirementException;

class VagrantPluginProcessor extends AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    public function process(string $name) : string
    {
        $output = parent::process($name);

        // If output does not contain '$name', it means that the vagrant plugin is not installed:
        if (false === strpos($output, $name)) {
            throw new MissingRequirementException();
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(string $name) : string
    {
        return 'vagrant plugin list';
    }
}
