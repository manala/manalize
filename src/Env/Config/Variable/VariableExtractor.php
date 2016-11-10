<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Env\Config\Variable;

/**
 * Variable property value extractor.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class VariableExtractor
{
    public function extract(Variable $var): array
    {
        $data = [];

        foreach ((new \ReflectionClass($var))->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($var);
        }

        return $data;
    }
}
