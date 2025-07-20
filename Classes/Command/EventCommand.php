<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\EventInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\EventCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EventCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly EventCreatorService $eventCreatorService,
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

        $this->eventCreatorService->create($this->askForEventInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForEventInformation(SymfonyStyle $io, InputInterface $input): EventInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
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

            if (preg_match('/^[0-9]/', $eventClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultEventClassName = $this->tryToCorrectEventClassName($eventClassName);
                $validEventClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $eventClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultEventClassName = $this->tryToCorrectEventClassName($eventClassName);
                $validEventClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $eventClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "ProcessRequestEvent".');
                $defaultEventClassName = $this->tryToCorrectEventClassName($eventClassName);
                $validEventClassName = false;
            } elseif (!str_ends_with($eventClassName, 'Event')) {
                $io->error('Class name must end with "Event".');
                $defaultEventClassName = $this->tryToCorrectEventClassName($eventClassName);
                $validEventClassName = false;
            } else {
                $validEventClassName = true;
            }
        } while (!$validEventClassName);

        return $eventClassName;
    }

    private function tryToCorrectEventClassName(string $givenEventClassName): string
    {
        // Remove invalid chars
        $cleanedEventClassName = preg_replace('/[^a-zA-Z0-9]/', '', $givenEventClassName);

        // Upper case first char
        $cleanedEventClassName = ucfirst($cleanedEventClassName);

        // Remove ending "evENt" with wrong case
        if (str_ends_with(strtolower($cleanedEventClassName), 'event')) {
            $cleanedEventClassName = substr($cleanedEventClassName, 0, -13);
        }

        // Add "Event" with correct case
        $cleanedEventClassName .= 'Event';

        return $cleanedEventClassName;
    }
}
