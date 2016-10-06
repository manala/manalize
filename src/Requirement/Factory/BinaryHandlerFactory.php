<?php

/*
 * This file is part of the Manala package.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Requirement\Factory;

use Manala\Requirement\Processor\BinaryProcessor;
use Manala\Requirement\SemVer\BinaryVersionParser;

/**
 * Factory that instantiates the concrete processor and version parser to handle binary requirements.
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
class BinaryHandlerFactory implements HandlerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProcessor()
    {
        return new BinaryProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionParser()
    {
        return new BinaryVersionParser();
    }
}
