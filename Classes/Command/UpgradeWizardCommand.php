<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\UpgradeWizardInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\UpgradeWizardCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpgradeWizardCommand extends Command
{
    use AskForExtensionKeyTrait;
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly UpgradeWizardCreatorService $upgradeWizardCreatorService,
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
            'We are here to assist you in creating a new TYPO3 Upgrade Wizard.',
            'Now, we will ask you a few questions to customize the upgrade wizard according to your needs.',
            'Please take your time to answer them.',
        ]);

        $upgradeWizardInformation = $this->askForUpgradeWizardInformation($io, $input);
        $this->upgradeWizardCreatorService->create($upgradeWizardInformation);
        $this->printCreatorInformation($upgradeWizardInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForUpgradeWizardInformation(SymfonyStyle $io, InputInterface $input): UpgradeWizardInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new UpgradeWizardInformation(
            $extensionInformation,
            $this->askForUpgradeWizardClassName($io),
        );
    }

    private function askForUpgradeWizardClassName(SymfonyStyle $io): string
    {
        $defaultUpgradeWizardClassName = null;

        do {
            $upgradeWizardClassName = (string)$io->ask(
                'Please provide the class name of your new Upgrade Wizard',
                $defaultUpgradeWizardClassName,
            );

            if (preg_match('/^\d/', $upgradeWizardClassName)) {
                $io->error('Class name should not start with a number.');
                $defaultUpgradeWizardClassName = $this->tryToCorrectClassName($upgradeWizardClassName, 'Upgrade');
                $validUpgradeWizardClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $upgradeWizardClassName)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultUpgradeWizardClassName = $this->tryToCorrectClassName($upgradeWizardClassName, 'Upgrade');
                $validUpgradeWizardClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $upgradeWizardClassName) === 0) {
                $io->error('Action must be written in UpperCamelCase like "CorrectPluginUpgrade".');
                $defaultUpgradeWizardClassName = $this->tryToCorrectClassName($upgradeWizardClassName, 'Upgrade');
                $validUpgradeWizardClassName = false;
            } elseif (!str_ends_with($upgradeWizardClassName, 'Upgrade')) {
                $io->error('Class name must end with "Upgrade".');
                $defaultUpgradeWizardClassName = $this->tryToCorrectClassName($upgradeWizardClassName, 'Upgrade');
                $validUpgradeWizardClassName = false;
            } else {
                $validUpgradeWizardClassName = true;
            }
        } while (!$validUpgradeWizardClassName);

        return $upgradeWizardClassName;
    }
}
