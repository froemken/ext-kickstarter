<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Command\Question\ValidClassNameQuestion;
use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\Information\CreatorInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\ControllerCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use StefanFroemken\ExtKickstarter\Validator\ActionNameValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ControllerCommand extends Command
{
    use AskForExtensionKeyTrait;
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use FileStructureBuilderTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly ControllerCreatorService $controllerCreatorService,
        private readonly ActionNameValidator $actionNameValidator,
        private readonly ValidClassNameQuestion $validClassNameQuestion,
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
            'We are here to assist you in creating a new TYPO3 controller.',
            'Now, we will ask you a few questions to customize the controller according to your needs.',
            'Please take your time to answer them.',
        ]);

        $controllerInformation = $this->askForControllerInformation($io, $input);
        $this->controllerCreatorService->create($controllerInformation);
        $this->printCreatorInformation($controllerInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForControllerInformation(SymfonyStyle $io, InputInterface $input): ControllerInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new ControllerInformation(
            $extensionInformation,
            $io->confirm('Do you prefer to create an extbase based controller?'),
            $this->validClassNameQuestion->ask(
                $io,
                'Please provide the name of your controller',
                'Controller',
                'Controller'
            ),
            $this->askForValidActionMethods($io),
            new CreatorInformation(),
        );
    }

    private function askForValidActionMethods(SymfonyStyle $io): array
    {
        $actionMethods = [];
        $default = 'indexAction';

        do {
            $input = (string)$io->ask('Please provide the name of your action method', $default);
            $result = $this->actionNameValidator->validate($input);

            if (!$result->isValid) {
                $io->error($result->error);
                $default = $result->suggestion;
            } else {
                $actionMethods[] = $input;
                $default = 'indexAction';
            }
        } while (!$result->isValid || $io->confirm('Do you want to add another action method?', false));

        return $actionMethods;
    }
}
