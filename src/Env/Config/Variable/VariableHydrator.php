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
 * Variable property value hydrator.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class VariableHydrator
{
    public function hydrate(Variable $var, array $data)
    {
        \Closure::bind(function (Variable $var, array $data) {
            foreach ($data as $property => $value) {
                $var->{$property} = $value;
            }
        }, $var, $var)->__invoke($var, $data);
    }
}
