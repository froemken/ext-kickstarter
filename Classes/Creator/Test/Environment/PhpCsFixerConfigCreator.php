<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Test\Environment;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\TestEnvInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PhpCsFixerConfigCreator implements TestEnvCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(TestEnvInformation $testEnvInformation): void
    {
        $cglPath = $testEnvInformation->getBuildPath() . 'cgl/';
        GeneralUtility::mkdir_deep($cglPath);
        $targetFile = $cglPath . '.php-cs-fixer.dist.php';
        if (is_file($targetFile)) {
            $testEnvInformation->getCreatorInformation()->fileExists(
                $targetFile
            );
            return;
        }
        $this->fileManager->createFile(
            $targetFile,
            $this->getTemplate(),
            $testEnvInformation->getCreatorInformation(),
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

if (PHP_SAPI !== 'cli') {
    die('This script supports command line usage only. Please check your command.');
}

return (new Config())
    ->setFinder(
        (new Finder())
            ->in(__DIR__ . '/../../')
            ->exclude(__DIR__ . '/../../.Build')
            ->exclude(__DIR__ . '/../../var')
    )
    ->setRiskyAllowed(true)
    ->setRules([
        '@DoctrineAnnotation' => true,
        // @todo: Switch to @PER-CS2.0 once php-cs-fixer's todo list is done: https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/7247
        '@PER-CS1.0' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => ['space' => 'none'],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'declare_parentheses' => true,
        'dir_constant' => true,
        // @todo: Can be dropped once we enable @PER-CS2.0
        'function_declaration' => [
            'closure_fn_spacing' => 'none',
        ],
        'function_to_constant' => ['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']],
        'type_declaration_spaces' => true,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
        'list_syntax' => ['syntax' => 'short'],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'method_argument_space' => true,
        'modernize_strpos' => true,
        'modernize_types_casting' => true,
        'native_function_casing' => true,
        'no_alias_functions' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'no_leading_namespace_whitespace' => true,
        'no_null_property_initialization' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_superfluous_elseif' => true,
        'no_trailing_comma_in_singleline' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_nullsafe_operator' => true,
        'nullable_type_declaration' => [
            'syntax' => 'question_mark',
        ],
        'nullable_type_declaration_for_default_null_value' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'php_unit_construct' => ['assertions' => ['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']],
        'php_unit_mock_short_will_return' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'phpdoc_no_access' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
        'return_type_declaration' => ['space_before' => 'none'],
        'single_quote' => true,
        'single_space_around_construct' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        // @todo: Can be dropped once we enable @PER-CS2.0
        'single_line_empty_body' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'whitespace_after_comma_in_array' => ['ensure_single_space' => true],
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    ]);
EOT;
    }
}
