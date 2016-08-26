<?php

$header = <<<EOF
This file is part of the Manala package.

(c) Manala <contact@manala.io>

For the full copyright and license information, please refer to the LICENSE
file that was distributed with this source code.
EOF;


$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(array(__DIR__.'/src', __DIR__.'/tests'))
;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        '-unalign_double_arrow',
        '-unalign_equals',
        '-psr0',
        'newline_after_open_tag',
        'ordered_use',
        'short_array_syntax',
        'header_comment',
    ))
    ->setUsingCache(false)
    ->finder($finder)
;
