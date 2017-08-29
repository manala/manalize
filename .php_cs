<?php

$header = <<<EOF
This file is part of the Manalize project.

(c) Manala <contact@manala.io>

For the full copyright and license information, please refer to the LICENSE
file that was distributed with this source code.
EOF;


$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests', __DIR__.'/bin'])
;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'psr0' => false,
        'binary_operator_spaces' => [
            'align_equals' => false,
            'align_double_arrow' => false,
        ],
        'ordered_imports' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => [
            'header' => $header,
            'commentType' => PhpCsFixer\Fixer\Comment\HeaderCommentFixer::HEADER_COMMENT,
        ],
    ])
;
