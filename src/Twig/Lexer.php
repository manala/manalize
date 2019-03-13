<?php

/*
 * This file is part of the Manalize project.
 *
 * (c) Manala <contact@manala.io>
 *
 * For the full copyright and license information, please refer to the LICENSE
 * file that was distributed with this source code.
 */

namespace Manala\Manalize\Twig;

use Twig\Environment;
use Twig\Lexer as BaseLexer;

/**
 * Manalize Twig Lexer.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class Lexer extends BaseLexer
{
    public function __construct(Environment $env)
    {
        parent::__construct($env, ['tag_comment' => ['[#', '#]'], 'tag_variable' => ['{#', '#}']]);
    }
}
