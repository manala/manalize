<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Requirement\Factory;

use Manala\Manalize\Requirement\Processor\AbstractProcessor;
use Manala\Manalize\Requirement\Processor\BinaryProcessor;
use Manala\Manalize\Requirement\SemVer\BinaryVersionParser;
use Manala\Manalize\Requirement\SemVer\VersionParserInterface;

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
    public function getProcessor(): AbstractProcessor
    {
        return new BinaryProcessor();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionParser(): VersionParserInterface
    {
        return new BinaryVersionParser();
    }
}
