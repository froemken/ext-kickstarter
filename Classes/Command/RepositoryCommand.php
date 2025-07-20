<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\RepositoryInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\RepositoryCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RepositoryCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly RepositoryCreatorService $repositoryCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Extbase Repository.',
            'Now, we will ask you a few questions to customize the repository according to your needs.',
            'Please take your time to answer them.',
        ]);

        $this->repositoryCreatorService->create($this->askForRepositoryInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForRepositoryInformation(SymfonyStyle $io, InputInterface $input): RepositoryInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new RepositoryInformation(
            $extensionInformation,
            $this->askForRepositoryClassName($io),
        );
    }

    private function askForRepositoryClassName(SymfonyStyle $io): string
    {
        $defaultRepositoryClassName = null;

        do {
            $repositoryClassName = (string)$io->ask(
                'Please provide the class name of your new Extbase Repository',
                $defaultRepositoryClassName,
            );

            if ($repositoryClassName === '') {
                $io->error('Class name can not be empty.');
                $validRepositoryClassName = false;
            } elseif (preg_match('/^[0-9]/', $repositoryClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultRepositoryClassName = $this->tryToCorrectRepositoryClassName($repositoryClassName);
                $validRepositoryClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $repositoryClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultRepositoryClassName = $this->tryToCorrectRepositoryClassName($repositoryClassName);
                $validRepositoryClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $repositoryClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "CarRepository".');
                $defaultRepositoryClassName = $this->tryToCorrectRepositoryClassName($repositoryClassName);
                $validRepositoryClassName = false;
            } elseif (!str_ends_with($repositoryClassName, 'Repository')) {
                $io->error('Class name must end with "Repository".');
                $defaultRepositoryClassName = $this->tryToCorrectRepositoryClassName($repositoryClassName);
                $validRepositoryClassName = false;
            } else {
                $validRepositoryClassName = true;
            }
        } while (!$validRepositoryClassName);

        return $repositoryClassName;
    }

    private function tryToCorrectRepositoryClassName(string $givenRepositoryClassName): string
    {
        // Remove invalid chars
        $cleanedRepositoryClassName = preg_replace('/[^a-zA-Z0-9]/', '', $givenRepositoryClassName);

        // Upper case first char
        $cleanedRepositoryClassName = ucfirst($cleanedRepositoryClassName);

        // Remove ending "rePOsiTory" with wrong case
        if (str_ends_with(strtolower($cleanedRepositoryClassName), 'repository')) {
            $cleanedRepositoryClassName = substr($cleanedRepositoryClassName, 0, -10);
        }

        // Add "Repository" with correct case
        $cleanedRepositoryClassName .= 'Repository';

        return $cleanedRepositoryClassName;
    }
}
