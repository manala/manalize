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
 * A single-value variable.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
abstract class SingleValue implements Variable
{
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaces(): array
    {
        return [
            sprintf('{{ %s }}', $this->getName()) => $this->value,
        ];
    }
}
