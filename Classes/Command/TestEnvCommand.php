<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Command;

use FriendsOfTYPO3\Kickstarter\Information\TestEnvInformation;
use FriendsOfTYPO3\Kickstarter\Service\Creator\TestEnvCreatorService;
use FriendsOfTYPO3\Kickstarter\Traits\AskForExtensionKeyTrait;
use FriendsOfTYPO3\Kickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestEnvCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly TestEnvCreatorService $testEnvCreatorService,
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
            'We are here to assist you in adding a test environment to your TYPO3 extension.',
            'Now, we will ask you a few questions to customize the testing environment according to your needs.',
            'Please take your time to answer them.',
        ]);

        $this->testEnvCreatorService->create($this->askForTestEnvInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForTestEnvInformation(SymfonyStyle $io, InputInterface $input): TestEnvInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new TestEnvInformation(
            $extensionInformation,
        );
    }
}
