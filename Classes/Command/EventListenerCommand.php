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
use StefanFroemken\ExtKickstarter\Command\Input\Question\EventListenerClassNameQuestion;
use StefanFroemken\ExtKickstarter\Command\Input\QuestionCollection;
use StefanFroemken\ExtKickstarter\Context\CommandContext;
use StefanFroemken\ExtKickstarter\Information\EventListenerInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\EventListenerCreatorService;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventListenerCommand extends Command
{
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly EventListenerCreatorService $eventListenerCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Event Listener.',
            'Now, we will ask you a few questions to customize the event listener according to your needs.',
            'Please take your time to answer them.',
        ]);

        $eventListenerInformation = $this->askForEventListenerInformation($commandContext);
        $this->eventListenerCreatorService->create($eventListenerInformation);
        $this->printCreatorInformation($eventListenerInformation->getCreatorInformation(), $commandContext);

        return Command::SUCCESS;
    }

    private function askForEventListenerInformation(CommandContext $commandContext): EventListenerInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            (string)$this->questionCollection->askQuestion(
                ChooseExtensionKeyQuestion::ARGUMENT_NAME,
                $commandContext,
            ),
            $commandContext
        );

        $eventListenerClassName = (string)$this->questionCollection->askQuestion(
            EventListenerClassNameQuestion::ARGUMENT_NAME,
            $commandContext,
        );

        return new EventListenerInformation(
            $extensionInformation,
            $eventListenerClassName,
        );
    }
}
