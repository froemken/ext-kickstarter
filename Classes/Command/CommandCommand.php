<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\CommandInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\CommandCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CommandCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly CommandCreatorService $commandCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Command.',
            'Now, we will ask you a few questions to customize the Command according to your needs.',
            'Please take your time to answer them.',
        ]);

        $this->commandCreatorService->create($this->askForCommandInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForCommandInformation(SymfonyStyle $io, InputInterface $input): CommandInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new CommandInformation(
            $extensionInformation,
            $this->askForCommandClassName($io),
            $this->askForCommandName($io),
            (string)$io->ask('Provide a description for your command'),
            $this->askForCommandAliases($io),
        );
    }

    private function askForCommandClassName(SymfonyStyle $io): string
    {
        $defaultCommandClassName = null;

        do {
            $commandClassName = (string)$io->ask(
                'Please provide the class name of your new Command',
                $defaultCommandClassName,
            );

            if ($commandClassName === '') {
                $io->error('Class name can not be empty.');
                $validCommandClassName = false;
            } elseif (preg_match('/^\d/', $commandClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultCommandClassName = $this->tryToCorrectCommandClassName($commandClassName);
                $validCommandClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $commandClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultCommandClassName = $this->tryToCorrectCommandClassName($commandClassName);
                $validCommandClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $commandClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "DoSomethingCommand".');
                $defaultCommandClassName = $this->tryToCorrectCommandClassName($commandClassName);
                $validCommandClassName = false;
            } elseif (!str_ends_with($commandClassName, 'Command')) {
                $io->error('Class name must end with "Command".');
                $defaultCommandClassName = $this->tryToCorrectCommandClassName($commandClassName);
                $validCommandClassName = false;
            } else {
                $validCommandClassName = true;
            }
        } while (!$validCommandClassName);

        return $commandClassName;
    }

    private function askForCommandName(SymfonyStyle $io): string
    {
        do {
            $commandName = (string)$io->ask(
                'Please provide the command name (example: "ext:clean")',
            );

            if ($commandName === '') {
                $io->error('Command name can not be empty.');
                $validCommandName = false;
            } elseif (preg_match('/^\d/', $commandName)) {
                $io->error('Command name should not start with a number.');
                $validCommandName = false;
            } elseif (preg_match('/[^a-zA-Z0-9:]/', $commandName)) {
                $io->error('Command name contains invalid chars. Please provide just letters, numbers and colon (:).');
                $validCommandName = false;
            } else {
                $validCommandName = true;
            }
        } while (!$validCommandName);

        return $commandName;
    }

    private function askForCommandAliases(SymfonyStyle $io): array
    {
        $commandAliases = (string)$io->ask(
            'Provide an alias name for your command',
        );

        if ($commandAliases === '') {
            return [];
        }

        return [$commandAliases];
    }

    private function tryToCorrectCommandClassName(string $givenCommandClassName): string
    {
        // Remove invalid chars
        $cleanedCommandClassName = preg_replace('/[^a-zA-Z0-9]/', '', $givenCommandClassName);

        // Upper case first char
        $cleanedCommandClassName = ucfirst($cleanedCommandClassName);

        // Remove ending "coMmaND" with wrong case
        if (str_ends_with(strtolower($cleanedCommandClassName), 'command')) {
            $cleanedCommandClassName = substr($cleanedCommandClassName, 0, -10);
        }

        // Add "Command" with correct case
        $cleanedCommandClassName .= 'Command';

        return $cleanedCommandClassName;
    }
}
