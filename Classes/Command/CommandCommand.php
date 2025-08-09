<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Command\Input\Question\ChooseExtensionKeyQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\CommandAliasQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\CommandClassNameQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\Question\CommandNameQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\QuestionCollection;
use StefanFroemken\ExtKickstarter\Context\CommandContext;
use StefanFroemken\ExtKickstarter\Information\CommandInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\CommandCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommandCommand extends Command
{
    use AskForExtensionKeyTrait;
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly CommandCreatorService $commandCreatorService,
        private readonly QuestionCollection $questionCollection,
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
            'We are here to assist you in creating a new TYPO3 Command.',
            'Now, we will ask you a few questions to customize the Command according to your needs.',
            'Please take your time to answer them.',
        ]);

        $commandInformation = $this->askForCommandInformation($commandContext);
        $this->commandCreatorService->create($commandInformation);
        $this->printCreatorInformation($commandInformation->getCreatorInformation(), $commandContext);

        return Command::SUCCESS;
    }

    private function askForCommandInformation(CommandContext $commandContext): CommandInformation
    {
        $io = $commandContext->getIo();
        $extensionInformation = $this->getExtensionInformation(
            (string)$this->questionCollection->askQuestion(
                ChooseExtensionKeyQuestion::ARGUMENT_NAME,
                $commandContext,
            ),
            $commandContext
        );
        $commandName = (string)$this->questionCollection->askQuestion(
            CommandNameQuestion::ARGUMENT_NAME,
            $commandContext,
            $extensionInformation->getExtensionKey().':doSomething'
        );
        $className =  (string)$this->questionCollection->askQuestion(
            CommandClassNameQuestion::ARGUMENT_NAME,
            $commandContext,
            $commandName
        );

        return new CommandInformation(
            $extensionInformation,
            $className,
            $commandName,
            (string)$io->ask('Provide a description for your command'),
            $this->askForCommandAliases($commandContext),
        );
    }

    private function askForCommandAliases(CommandContext $commandContext): array
    {
        $commandAliases = [];
        while ($commandContext->getIo()->confirm('Do you want to add (another) command alias? ', false)) {
            $commandAliases[] = (string)$this->questionCollection->askQuestion(
                CommandAliasQuestion::ARGUMENT_NAME,
                $commandContext,
            );
        }

        return $commandAliases;
    }
}
