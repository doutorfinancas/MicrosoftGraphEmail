<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__)
    ->path('src/')
    ->path('tests/')
    ->notPath('vendor/')
    ->notName('autoload.php')
;

$config = new PhpCsFixer\Config();
$config->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'no_unused_imports' => true,
        'class_attributes_separation' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => false,
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_align' => false,
        'function_typehint_space' => true
    ])
    ->setFinder($finder)
;

return $config;
