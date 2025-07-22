<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Command\Question\ChoseExtensionKeyQuestion;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\ModelInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\ModelCreatorService;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ModelCommand extends Command
{
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    private const DATA_TYPES = [
        'array',
        'bool',
        'float',
        'int',
        'string',
        'object',
        \DateTime::class,
        ObjectStorage::class,
    ];

    public function __construct(
        private readonly ModelCreatorService $modelCreatorService,
        private readonly ChoseExtensionKeyQuestion $choseExtensionKeyQuestion,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'extension_key',
            InputArgument::OPTIONAL,
            'Provide the extension key you want to extend.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to the TYPO3 Extension Builder');

        $io->text([
            'We are here to assist you in creating a new TYPO3 Extbase Model.',
            'Now, we will ask you a few questions to customize the model according to your needs.',
            'Please take your time to answer them.',
        ]);

        $modelInformation = $this->askForModelInformation($io, $input);
        $this->modelCreatorService->create($modelInformation);
        $this->printCreatorInformation($modelInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForModelInformation(SymfonyStyle $io, InputInterface $input): ModelInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->choseExtensionKeyQuestion->ask($io, $input->getArgument('extension_key')),
            $io
        );

        $modelClassName = $this->askForModelClassName($io);
        $mappedTableName = $this->askForMappedTableName($io, $modelClassName, $extensionInformation);

        return new ModelInformation(
            $extensionInformation,
            $modelClassName,
            $mappedTableName,
            $io->confirm('Should your model be created as entity? Else, it will be created as value object.'),
            $this->askForProperties($io, $mappedTableName, $extensionInformation),
        );
    }

    private function askForModelClassName(SymfonyStyle $io): string
    {
        $defaultModelClassName = null;

        do {
            $modelClassName = (string)$io->ask(
                'Please provide the class name of your new Extbase Model',
                $defaultModelClassName,
            );

            if (preg_match('/^\d/', $modelClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultModelClassName = $this->tryToCorrectClassName($modelClassName);
                $validModelClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $modelClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultModelClassName = $this->tryToCorrectClassName($modelClassName);
                $validModelClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $modelClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "Blog".');
                $defaultModelClassName = $this->tryToCorrectClassName($modelClassName);
                $validModelClassName = false;
            } else {
                $validModelClassName = true;
            }
        } while (!$validModelClassName);

        return $modelClassName;
    }

    private function askForMappedTableName(
        SymfonyStyle $io,
        string $modelClassName,
        ExtensionInformation $extensionInformation
    ): string {
        $configuredTcaTables = $extensionInformation->getConfiguredTcaTables();

        if ($configuredTcaTables === []) {
            $io->error([
                'There are no TCA tables configured within your extension.',
                'Please create a TCA table with command "make:table" first.',
            ]);
            die();
        }

        $expectedTableName = sprintf(
            'tx_%s_domain_model_%s',
            str_replace('_', '', $extensionInformation->getExtensionKey()),
            strtolower($modelClassName),
        );

        $io->info([
            'Your domain model "' . $modelClassName . '" has to be mapped to a TCA table.',
            'In following list you see the configured TCA tables within your extension.',
        ]);

        return $io->choice(
            'Chose the TCA table you want to map your model to',
            $configuredTcaTables,
            in_array($expectedTableName, $configuredTcaTables, true) ? $expectedTableName : null,
        );
    }

    private function askForProperties(
        SymfonyStyle $io,
        string $mappedTableName,
        ExtensionInformation $extensionInformation
    ): array {
        $properties = [];
        $tableTca = $extensionInformation->getTcaForTable($mappedTableName);

        // Step 1: Handle domain fields
        $domainColumns = $extensionInformation->getDomainColumnNamesFromTca($tableTca);
        $domainFieldChoice = $io->choice(
            'Which domain fields should be included',
            ['All', 'Choose manually'],
            'All'
        );

        $selectedDomainColumns = [];
        if ($domainFieldChoice === 'All') {
            $selectedDomainColumns = $domainColumns;
        } else {
            $selectedDomainColumns = $io->choice(
                'Select the domain fields you want to include in your model',
                $domainColumns,
                null,
                true
            );
        }

        // Step 2: Ask about system fields (all, none, or custom)
        $systemColumns = $extensionInformation->getSystemColumnNamesFromTca($tableTca);
        $systemFieldChoice = $io->choice(
            'Which system fields should be included',
            ['All', 'None', 'Choose manually'],
            'None'
        );

        $selectedSystemColumns = [];
        if ($systemFieldChoice === 'All') {
            $selectedSystemColumns = $systemColumns;
        } elseif ($systemFieldChoice === 'Choose manually') {
            $selectedSystemColumns = $io->choice(
                'Select the system fields to include',
                $systemColumns,
                null,
                true
            );
        }

        // Combine selected columns
        $selectedColumns = array_merge($selectedDomainColumns, $selectedSystemColumns);

        // Process selected columns into properties
        foreach ($selectedColumns as $columnName) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($columnName);

            $dataType = $io->choice(
                "Which data type you prefer for your property: \"{$propertyName}\"",
                self::DATA_TYPES,
                'string'
            );

            // Basic meta
            $properties[$columnName] = [
                'propertyName' => $propertyName,
                'dataType' => $io->choice(
                    'Which data type do you prefer for the property "' . $propertyName . '"',
                    self::DATA_TYPES,
                    'string'
                ),
            ];

            // handle object-initializable types
            if (!in_array($dataType, ['int', 'float', 'string', 'bool', 'array'], true)) {
                $properties[$columnName]['initializeObject'] = true;
                continue;
            }

            // 1) read TCA default, 2) convert to native, 3) ask user (pre-filled)
            $tcaDefault     = (string)($tableTca['columns'][$columnName]['config']['default'] ?? '');
            $defaultValue   = $this->askForDefaultValue($io, $propertyName, $dataType, $tcaDefault);

            $properties[$columnName]['defaultValue'] = $defaultValue;
        }

        return $properties;
    }

    private function askForDefaultValue(
        SymfonyStyle $io,
        string $propertyName,
        string $dataType,
        ?string $suggestedDefault = null
    ): mixed {
        return match ($dataType) {
            'int' => (int)$io->ask("Default value for '$propertyName' (int)", $suggestedDefault ?? '0'),
            'float' => (float)$io->ask("Default value for '$propertyName' (float)", $suggestedDefault ?? '0.0'),
            'bool' => $io->confirm("Default value for '$propertyName' (bool)?", (bool)$suggestedDefault),
            'string' => (string)$io->ask("Default value for '$propertyName' (string)", $suggestedDefault ?? ''),
            'array' => json_decode(
                (string)$io->ask("Default value for '$propertyName' (array, JSON format)", $suggestedDefault !== null && $suggestedDefault !== '' && $suggestedDefault !== '0' ? json_encode($suggestedDefault) : '[]'),
                true
            ),
            default => null,
        };
    }
}
