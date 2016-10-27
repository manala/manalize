<?php

$header = <<<EOF
This file is part of the Manalize project.

(c) Manala <contact@manala.io>

For the full copyright and license information, please refer to the LICENSE
file that was distributed with this source code.
EOF;


$finder = PhpCsFixer\Finder::create()
    ->in(array(__DIR__.'/src', __DIR__.'/tests', __DIR__.'/bin'))
;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->finder($finder)
    ->setRules([
        '@Symfony' => true,
        'psr0' => false,
        'unalign_equals' => false,
        'unalign_double_arrow' => false,
        'ordered_imports' => true,
        'short_array_syntax' => true,
        'header_comment' => [
            'header' => $header,
            'commentType' => PhpCsFixer\Fixer\Comment\HeaderCommentFixer::HEADER_COMMENT,
        ],
    ])
;
