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
use Manala\Manalize\Requirement\SemVer\VersionParserInterface;

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
    public function getProcessor(): AbstractProcessor;

    /**
     * @return VersionParserInterface
     */
    public function getVersionParser(): VersionParserInterface;
}
