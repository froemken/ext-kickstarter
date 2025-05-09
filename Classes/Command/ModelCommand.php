<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\ModelInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\ModelCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class ModelCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

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

        $this->modelCreatorService->create($this->askForModelInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForModelInformation(SymfonyStyle $io, InputInterface $input): ModelInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
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
                'Please provide the class name of your new Extbase Model.',
                $defaultModelClassName,
            );

            if (preg_match('/^[0-9]/', $modelClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultModelClassName = $this->tryToCorrectModelClassName($modelClassName);
                $validModelClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $modelClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultModelClassName = $this->tryToCorrectModelClassName($modelClassName);
                $validModelClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $modelClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "Blog".');
                $defaultModelClassName = $this->tryToCorrectModelClassName($modelClassName);
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

        $columnNames = $io->choice(
            'Which column names should be used for your model?',
            $extensionInformation->getColumnNamesFromTca($tableTca),
            null,
            true
        );

        foreach ($columnNames as $columnName) {
            $propertyName = GeneralUtility::underscoredToLowerCamelCase($columnName);

            $properties[$columnName] = [
                'propertyName' => $propertyName,
                'tcaType' => $tableTca['columns'][$columnName]['config']['type'] ?? 'input',
                'dataType' => $io->choice(
                    'Which data type you prefer for your property: "' . $propertyName . '"?',
                    self::DATA_TYPES,
                    'string'
                ),
            ];
        }

        return $properties;
    }

    private function tryToCorrectModelClassName(string $givenModelClassName): string
    {
        // Remove invalid chars
        $cleanedModelClassName = preg_replace('/[^a-zA-Z0-9]/', '', $givenModelClassName);

        // Upper case first char
        return ucfirst($cleanedModelClassName);
    }
}
