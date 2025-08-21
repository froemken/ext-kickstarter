<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command;

use FriendsOfTYPO3\Kickstarter\Command\Input\QuestionAttributeCollection;
use FriendsOfTYPO3\Kickstarter\Command\Input\QuestionCollection;
use FriendsOfTYPO3\Kickstarter\Context\CommandContext;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionMappingInformation;
use FriendsOfTYPO3\Kickstarter\Information\TableColumnInformation;
use FriendsOfTYPO3\Kickstarter\Information\TableInformation;
use FriendsOfTYPO3\Kickstarter\Parser\ExtensionInformationParser;
use FriendsOfTYPO3\Kickstarter\Service\Creator\TableCreatorService;
use FriendsOfTYPO3\Kickstarter\Traits\CreatorInformationTrait;
use FriendsOfTYPO3\Kickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TableCommand extends Command
{
    use CreatorInformationTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly TableCreatorService $tableCreatorService,
        private readonly QuestionCollection $questionCollection,
        private readonly QuestionAttributeCollection $questionAttributeCollection,
        private readonly ExtensionInformationParser $extensionInformationParser,
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
        $commandContext = new CommandContext($input, $output);
        $io = $commandContext->getIo();
        $io->title('Welcome to the TYPO3 Extension Builder');

        $io->text([
            'We are here to assist you in creating a new TCA table.',
            'Now, we will ask you a few questions to customize the controller according to your needs.',
            'Please take your time to answer them.',
        ]);

        $tableInformation = $this->askForTableInformation($commandContext);
        $this->tableCreatorService->create($tableInformation);
        $this->printCreatorInformation($tableInformation->getCreatorInformation(), $commandContext);

        return Command::SUCCESS;
    }

    private function askForTableInformation(CommandContext $commandContext): TableInformation
    {
        $tableInformation = new TableInformation();
        $extensionMappingInformation = new ExtensionMappingInformation();
        $this->questionAttributeCollection->askQuestion(
            $extensionMappingInformation,
            'extensionKey',
            $commandContext,
        );
        $tableInformation->setExtensionInformation($this->extensionInformationParser->parse($extensionMappingInformation));

        $this->questionAttributeCollection->askQuestion(
            $tableInformation,
            'title',
            $commandContext,
        );
        $this->questionAttributeCollection->askQuestion(
            $tableInformation,
            'tableName',
            $commandContext,
        );
        $columns = [];
        do {
            $column = new TableColumnInformation();
            $column->setTableInformation($tableInformation);
            $this->questionAttributeCollection->askQuestion(
                $column,
                'label',
                $commandContext,
            );
            $this->questionAttributeCollection->askQuestion(
                $column,
                'columnName',
                $commandContext,
            );
            $this->questionAttributeCollection->askQuestion(
                $column,
                'type',
                $commandContext,
            );
            $columns[$column->getColumnName()] = $column;
        } while ($commandContext->getIo()->confirm('Do you want to add another table column?', false));
        $tableInformation->setColumns($columns);
        return $tableInformation;
    }
}
