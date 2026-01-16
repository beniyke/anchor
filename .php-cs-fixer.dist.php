<?php

/**
 * PHP-CS-Fixer configuration for Anchor Framework
 * Complements Pint with additional formatting rules matching phpfmt preferences
 */

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/App',
        __DIR__ . '/System',
    ])
    ->exclude([
        'App/storage',
        'vendor',
        'packages',
        'public'
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,

        // Array formatting - single space after =>
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'single_space',
            ],
        ],

        // New line before return
        'blank_line_before_statement' => ['statements' => ['return']],

        // Strict types declaration
        'declare_strict_types' => true,

        // Method and property spacing
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],

        // Echo tags
        'echo_tag_syntax' => ['format' => 'short'],

        // Imports
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],

        // PHPDoc alignment and formatting
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_separation' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'alpha',
        ],

        // Semicolons
        'semicolon_after_instruction' => false,

        // Additional code quality rules
        'concat_space' => ['spacing' => 'none'],
        'single_quote' => true,
        'no_whitespace_in_blank_line' => true,
        'ternary_to_null_coalescing' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
