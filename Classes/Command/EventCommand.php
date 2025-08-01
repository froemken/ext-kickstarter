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
use StefanFroemken\ExtKickstarter\Information\EventInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\EventCreatorService;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventCommand extends Command
{
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly EventCreatorService $eventCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Event.',
            'Now, we will ask you a few questions to customize the event according to your needs.',
            'Please take your time to answer them.',
        ]);

        $eventInformation = $this->askForEventInformation($io, $input);
        $this->eventCreatorService->create($eventInformation);
        $this->printCreatorInformation($eventInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForEventInformation(SymfonyStyle $io, InputInterface $input): EventInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->choseExtensionKeyQuestion->ask($io, $input->getArgument('extension_key')),
            $io
        );

        return new EventInformation(
            $extensionInformation,
            $this->askForEventClassName($io),
        );
    }

    private function askForEventClassName(SymfonyStyle $io): string
    {
        $defaultEventClassName = null;

        do {
            $eventClassName = (string)$io->ask(
                'Please provide the class name of your new Event',
                $defaultEventClassName,
            );

            if (preg_match('/^\d/', $eventClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultEventClassName = $this->tryToCorrectClassName($eventClassName, 'Event');
                $validEventClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $eventClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultEventClassName = $this->tryToCorrectClassName($eventClassName, 'Event');
                $validEventClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $eventClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "ProcessRequestEvent".');
                $defaultEventClassName = $this->tryToCorrectClassName($eventClassName, 'Event');
                $validEventClassName = false;
            } elseif (!str_ends_with($eventClassName, 'Event')) {
                $io->error('Class name must end with "Event".');
                $defaultEventClassName = $this->tryToCorrectClassName($eventClassName, 'Event');
                $validEventClassName = false;
            } else {
                $validEventClassName = true;
            }
        } while (!$validEventClassName);

        return $eventClassName;
    }
}
