<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Exception;

use Manala\Env\EnvEnum;

class InvalidEnvException extends \InvalidArgumentException
{
    public function __construct($invalidEnvName)
    {
        parent::__construct(sprintf(
            'The env "%s" doesn\'t exist. Possible values: [%s]',
            $invalidEnvName,
            implode(' ', EnvEnum::getPossibleEnvs())
        ));
    }
}
