<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude(['vendor'])
    ->name('*.php')
    ->notName('*.blade.php');

return (new Config())
    ->setRiskyAllowed(true)    ->setRules([
        // PSR-12 standard
        '@PSR12' => true,
        '@PhpCsFixer' => true,
        
        // Fix conflicting rules - disable the conflicting one from @PhpCsFixer
        'blank_lines_before_namespace' => false,
        
        // Allow flexible namespace usage - don't force leading backslashes
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'no_unused_imports' => true,
        'fully_qualified_strict_types' => false,
        
        // Additional rules for professional code
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space'],
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'for', 'foreach', 'while', 'do', 'switch'],
        ],
        'cast_spaces' => true,
        'class_attributes_separation' => [
            'elements' => ['method' => 'one', 'property' => 'one'],
        ],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'function_typehint_space' => true,
        'include' => true,
        'lowercase_cast' => true,
        'native_function_casing' => true,
        'new_with_braces' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use',
            ],
        ],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => ['use' => 'echo'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag_normalizer' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => true,
        'return_type_declaration' => true,
        'semicolon_after_instruction' => true,
        'short_scalar_cast' => true,
        'single_blank_line_before_namespace' => true,
        'single_class_element_per_statement' => true,
        'single_import_per_statement' => true,
        'single_line_after_imports' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'single_quote' => true,
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => true,
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'yoda_style' => true,
    ])
    ->setFinder($finder);
