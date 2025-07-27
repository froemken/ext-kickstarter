<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\EventListenerInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\EventListenerCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventListenerCommand extends Command
{
    use AskForExtensionKeyTrait;
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly EventListenerCreatorService $eventListenerCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Event Listener.',
            'Now, we will ask you a few questions to customize the event listener according to your needs.',
            'Please take your time to answer them.',
        ]);

        $eventListenerInformation = $this->askForEventListenerInformation($io, $input);
        $this->eventListenerCreatorService->create($eventListenerInformation);
        $this->printCreatorInformation($eventListenerInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForEventListenerInformation(SymfonyStyle $io, InputInterface $input): EventListenerInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new EventListenerInformation(
            $extensionInformation,
            $this->askForEventListenerClassName($io),
        );
    }

    private function askForEventListenerClassName(SymfonyStyle $io): string
    {
        $defaultEventListenerClassName = null;

        do {
            $eventListenerClassName = (string)$io->ask(
                'Please provide the class name of your new Event Listener',
                $defaultEventListenerClassName,
            );

            if (preg_match('/^\d/', $eventListenerClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultEventListenerClassName = $this->tryToCorrectClassName($eventListenerClassName, 'EventListener');
                $validEventListenerClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $eventListenerClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultEventListenerClassName = $this->tryToCorrectClassName($eventListenerClassName, 'EventListener');
                $validEventListenerClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $eventListenerClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "HandleRequestEventListener".');
                $defaultEventListenerClassName = $this->tryToCorrectClassName($eventListenerClassName, 'EventListener');
                $validEventListenerClassName = false;
            } elseif (!str_ends_with($eventListenerClassName, 'EventListener')) {
                $io->error('Class name must end with "EventListener".');
                $defaultEventListenerClassName = $this->tryToCorrectClassName($eventListenerClassName, 'EventListener');
                $validEventListenerClassName = false;
            } else {
                $validEventListenerClassName = true;
            }
        } while (!$validEventListenerClassName);

        return $eventListenerClassName;
    }
}
