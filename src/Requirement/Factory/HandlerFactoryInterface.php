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

use Manala\Requirement\Processor\AbstractProcessor;
use Manala\Requirement\SemVer\VersionParserInterface;

/**
 * Interface for factories that instantiate the proper processor and version parser for a given type of requirement
 * (eg binary or vagrant plugin).
 *
 * @author Xavier Roldo <xavier.roldo@elao.com>
 */
interface HandlerFactoryInterface
{
    /**
     * @return AbstractProcessor
     */
    public function getProcessor();

    /**
     * @return VersionParserInterface
     */
    public function getVersionParser();
}
