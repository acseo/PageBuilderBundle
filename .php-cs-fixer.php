<?php

$fileHeaderComment = <<<COMMENT
This file is part of the ACSEO/PageBuilder package.

(c) ACSEO <contact@acseo.fr>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->notName('Kernel.php')
    ->notName('bootstrap.php')
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'header_comment' => ['header' => $fileHeaderComment, 'separate' => 'both'],
        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder)
;