<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Creator\Tca\TcaTableCreator;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\TableInformation;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TableCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly TcaTableCreator $tcaTableCreator,
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

        $tableInformation = $this->askForTableInformation($io, $input);

        $this->tcaTableCreator->create($tableInformation);

        return Command::SUCCESS;
    }

    private function askForTableInformation(SymfonyStyle $io, InputInterface $input): TableInformation
    {
        do {
            $extensionInformation = $this->getExtensionInformation(
                $this->askForExtensionKey($io, $input->getArgument('extension_key'))
            );

            if (!is_dir($extensionInformation->getExtensionPath())) {
                $io->error(sprintf(
                    '%s: %s',
                        'Can not access extension directory. Please check extension key. Extension path',
                        $extensionInformation->getExtensionPath(),
                    )
                );
                $validExtensionPath = false;
            } else {
                $validExtensionPath = true;
            }
        } while (!$validExtensionPath);

        return new TableInformation(
            $extensionInformation,
            $this->askForTableName($io, $extensionInformation),
            (string)$io->ask('Please provide a table title'),
            'uid', // Until now, we do not have any defined columns. We set label to "uid" first as it is mandatory
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
}
