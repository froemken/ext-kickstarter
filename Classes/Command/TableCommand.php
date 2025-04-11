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
use StefanFroemken\ExtKickstarter\Information\TableInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\TableCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TableCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly TableCreatorService $tableCreatorService,
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
            'We are here to assist you in creating a new TCA table.',
            'Now, we will ask you a few questions to customize the controller according to your needs.',
            'Please take your time to answer them.',
        ]);

        $this->tableCreatorService->create($this->askForTableInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForTableInformation(SymfonyStyle $io, InputInterface $input): TableInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new TableInformation(
            $extensionInformation,
            $this->askForTableName($io, $extensionInformation),
            (string)$io->ask('Please provide a table title'),
            'uid', // Until now, we do not have any defined columns. We set label to "uid" first as it is mandatory
            $this->askForTableColumns($io),
        );
    }

    private function askForTableName(SymfonyStyle $io, ExtensionInformation $extensionInformation): string
    {
        $tableName = (string)$io->ask(
            'Please provide the table name. Usually the table name starts with: ' . $extensionInformation->getTableNamePrefix(),
        );

        $tableName = strtolower($tableName);
        if (str_starts_with($tableName, $extensionInformation->getTableNamePrefix())) {
            // User has entered full expected table name. Use it.
            return $tableName;
        }

        // User has entered something unexpected like "cars". Let him ask about "tx_myext_domain_model_cars"
        $isTableNameConfirmed = (string)$io->confirm(
            'Would you like to adopt the suggested table name: ' . $extensionInformation->getTableNamePrefix() . $tableName . '?',
        );

        if ($isTableNameConfirmed) {
            return $extensionInformation->getTableNamePrefix() . $tableName;
        }

        return $tableName;
    }

    private function askForTableColumns(SymfonyStyle $io): array
    {
        $tableColumns = [];
        $validTableColumnName = false;
        $defaultColumnName = null;

        do {
            $tableColumnName = (string)$io->ask('Enter column name we should create for you', $defaultColumnName);

            if (preg_match('/^[0-9]/', $tableColumnName)) {
                $io->error('Table column should not start with a number.');
                $defaultColumnName = $this->tryToCorrectColumnName($tableColumnName);
                $validTableColumnName = false;
            } elseif (preg_match('/[^a-z0-9_]/', $tableColumnName)) {
                $io->error('Table column name contains invalid chars. Please provide just letters, numbers and underscores.');
                $defaultColumnName = $this->tryToCorrectColumnName($tableColumnName);
                $validTableColumnName = false;
            } else {
                $tableColumns[$tableColumnName]['label'] = $io->ask(
                    'Please provide a label for the column',
                    ucwords(str_replace('_', ' ', $tableColumnName))
                );
                $tableColumns[$tableColumnName]['config'] = $this->askForTableColumnConfiguration($tableColumnName, $io);
                if ($io->confirm('Do you want to add another table column?')) {
                    continue;
                }
                $validTableColumnName = true;
            }
        } while (!$validTableColumnName);

        return $tableColumns;
    }

    private function tryToCorrectColumnName(string $givenColumnName): string
    {
        // Change dash to underscore
        $cleanedColumnName = str_replace('-', '_', $givenColumnName);

        // Change column name to lower camel case. Add underscores before upper case letters. BlogExample => blog_example
        $cleanedColumnName = GeneralUtility::camelCaseToLowerCaseUnderscored($cleanedColumnName);

        // Remove invalid chars
        return preg_replace('/[^a-zA-Z0-9_]/', '', $cleanedColumnName);
    }

    private function askForTableColumnConfiguration(string $tableColumnName, SymfonyStyle $io): array
    {
        $tableColumnType = $io->choice('Choose TCA column type', array_keys(self::TABLE_COLUMN_TYPES), 'input');

        return TableCreatorService::TABLE_COLUMN_TYPES[$tableColumnType] ?? [];
    }
}
