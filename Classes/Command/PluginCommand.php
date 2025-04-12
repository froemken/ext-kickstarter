<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\PluginCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PluginCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionInformationTrait;
    use FileStructureBuilderTrait;

    public function __construct(
        private readonly PluginCreatorService $pluginCreatorService,
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
            'We are here to assist you in creating a new TYPO3 plugin.',
            'Now, we will ask you a few questions to customize the plugin according to your needs.',
            'Please take your time to answer them.',
        ]);

        $this->pluginCreatorService->create($this->askForPluginInformation($io, $input));

        return Command::SUCCESS;
    }

    private function askForPluginInformation(SymfonyStyle $io, InputInterface $input): PluginInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        $pluginLabel = (string)$io->ask(
            'Please provide a label for your plugin. You will see the label in the backend.',
        );
        $pluginName = $this->askForPluginName($io, $pluginLabel);

        $referencedControllerActions = [];
        $isExtbasePlugin = $io->confirm('Do you prefer to create an extbase based plugin?');
        if ($isExtbasePlugin) {
            $extbaseControllerClassnames = $extensionInformation->getExtbaseControllerClassnames();
            if ($extbaseControllerClassnames === []) {
                $io->error([
                    'Your extension does not contain any extbase controllers.',
                    'Please create at least one extbase controller with \'typo3 make:controller\' before creating a plugin.',
                ]);
                die();
            }

            $referencedControllerActions = $this->askForReferencedControllerActions(
                $io,
                $extbaseControllerClassnames,
                $extensionInformation,
            );
        }

        return new PluginInformation(
            $extensionInformation,
            $isExtbasePlugin,
            $pluginLabel,
            $pluginName,
            $referencedControllerActions,
        );
    }

    private function askForPluginName(
        SymfonyStyle $io,
        string $pluginLabel,
    ): string {
        return (string)$io->ask(
            'Please provide the name of your plugin. This is an internal identifier and will be used to reference your plugin in the backend.',
            GeneralUtility::underscoredToUpperCamelCase(str_replace(' ', '_', $pluginLabel)),
        );
    }

    private function askForReferencedControllerActions(
        SymfonyStyle $io,
        array $extbaseControllerClassnames,
        ExtensionInformation $extensionInformation,
    ): array {
        $referencedControllerActions = [];

        $referencedExtbaseControllerNames = (array)$io->choice(
            'Select the extbase controller classes you want to reference to your plugin.',
            $extbaseControllerClassnames,
            null,
            true
        );

        foreach ($referencedExtbaseControllerNames as $referencedExtbaseControllerName) {
            $referencedControllerActions[$referencedExtbaseControllerName]['cached'] = $io->choice(
                'Select the CACHED actions for your controller ' . $referencedExtbaseControllerName . ' you want to reference to your plugin.',
                $extensionInformation->getExtbaseControllerActionNames($referencedExtbaseControllerName),
                null,
                true
            );
            $referencedControllerActions[$referencedExtbaseControllerName]['uncached'] = $io->choice(
                'Select the UNCACHED actions for your controller ' . $referencedExtbaseControllerName . ' you want to reference to your plugin.',
                $extensionInformation->getExtbaseControllerActionNames($referencedExtbaseControllerName),
                null,
                true
            );
        }

        return $referencedControllerActions;
    }
}
