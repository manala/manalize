<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Exception;

use Manala\Manalize\Env\EnvEnum;

class InvalidEnvException extends \InvalidArgumentException
{
    public function __construct($invalidEnvName)
    {
        parent::__construct(sprintf(
            'The env "%s" doesn\'t exist. Possible values: %s',
            $invalidEnvName,
            json_encode(EnvEnum::getPossibleEnvs())
        ));
    }
}
