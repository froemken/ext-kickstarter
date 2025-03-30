<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Creator\Controller\ControllerCreator;
use StefanFroemken\ExtKickstarter\Creator\Controller\ExtbaseControllerCreator;
use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ControllerCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;

    public function __construct(
        private readonly ExtbaseControllerCreator $extbaseControllerCreator,
        private readonly ControllerCreator $controllerCreator,
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

        if ($controllerInformation->isExtbaseController() === true) {
            $this->extbaseControllerCreator->create($controllerInformation);
        } else {
            $this->controllerCreator->create($controllerInformation);
        }

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
            $this->askForControllerName($io),
            $this->askForActionMethodNames($io),
        );
    }

    private function askForActionMethodNames(SymfonyStyle $io): array
    {
        $actionMethods = [];
        $validActionName = false;
        $defaultActionName = 'indexAction';

        do {
            $actionMethod = (string)$io->ask(
                'Please provide the name of your action method.',
                $defaultActionName,
            );

            if (preg_match('/^[0-9]/', $actionMethod)) {
                $io->error('Action name should not start with a number.');
                $defaultActionName = $this->tryToCorrectActionname($actionMethod);
                $validActionName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $actionMethod)) {
                $io->error('Action name contains invalid chars. Please provide just letters and numbers.');
                $defaultActionName = $this->tryToCorrectActionname($actionMethod);
                $validActionName = false;
            } elseif (preg_match('/^[a-z0-9]+$/', $actionMethod)) {
                $io->error('Action must be written in LowerCamelCase like showAction.');
                $defaultActionName = $this->tryToCorrectActionname($actionMethod);
                $validActionName = false;
            } elseif (!str_ends_with($actionMethod, 'Action')) {
                $io->error('Action must end with "Action".');
                $defaultActionName = $this->tryToCorrectActionname($actionMethod);
                $validActionName = false;
            } else {
                $actionMethods[] = $actionMethod;
                if ($io->confirm('Do you want to add another action method?')) {
                    continue;
                }
                $validActionName = true;
            }
        } while (!$validActionName);

        return $actionMethods;
    }

    private function askForControllerName(SymfonyStyle $io): string
    {
        $defaultControllerName = null;
        do {
            $controllerName = (string)$io->ask(
                'Please provide the name of your controller.',
                $defaultControllerName,
            );

            if (preg_match('/^[0-9]/', $controllerName)) {
                $io->error('Controller name should not start with a number.');
                $defaultControllerName = $this->tryToCorrectControllerName($controllerName);
                $validControllerName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $controllerName)) {
                $io->error('Controller name contains invalid chars. Please provide just letters and numbers.');
                $defaultControllerName = $this->tryToCorrectControllerName($controllerName);
                $validControllerName = false;
            } elseif (preg_match('/^[a-z0-9]+$/', $controllerName)) {
                $io->error('Controller must be written in UpperCamelCase like BlogExampleController.');
                $defaultControllerName = $this->tryToCorrectControllerName($controllerName);
                $validControllerName = false;
            } elseif (!str_ends_with($controllerName, 'Controller')) {
                $io->error('Controller must end with "Controller".');
                $defaultControllerName = $this->tryToCorrectControllerName($controllerName);
                $validControllerName = false;
            } else {
                $validControllerName = true;
            }
        } while (!$validControllerName);

        return $controllerName;
    }

    private function tryToCorrectControllerName(string $givenControllerName): string
    {
        // Remove invalid chars
        $cleanedControllerName = preg_replace('/[^a-zA-Z0-9]/', '', $givenControllerName);

        // Upper case first char
        $cleanedControllerName = ucfirst($cleanedControllerName);

        // Remove ending "cOntroLLeR" with wrong case
        if (str_ends_with(strtolower($cleanedControllerName), 'controller')) {
            $cleanedControllerName = substr($cleanedControllerName, 0, -10);
        }

        // Add "Controller" with correct case
        $cleanedControllerName .= 'Controller';

        return $cleanedControllerName;
    }

    private function tryToCorrectActionname(string $givenActionName): string
    {
        // Remove invalid chars
        $cleanedActionName = preg_replace('/[^a-zA-Z0-9]/', '', $givenActionName);

        // Lower case first char
        $cleanedActionName = lcfirst($cleanedActionName);

        // Remove ending "aCtioN" with wrong case
        if (str_ends_with(strtolower($cleanedActionName), 'action')) {
            $cleanedActionName = substr($cleanedActionName, 0, -6);
        }

        // Add "Controller" with correct case
        $cleanedActionName .= 'Action';

        return $cleanedActionName;
    }
}
