<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use StefanFroemken\ExtKickstarter\Creator\Extension\ExtensionCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Plugin\ExtbasePluginCreator;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Model\Node;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @param iterable<ExtensionCreatorInterface> $creators
 */
class PluginCommand extends Command
{
    use AskForExtensionKeyTrait;
    use ExtensionPathTrait;

    public function __construct(
        private readonly ExtbasePluginCreator $extbasePluginCreator,
    ) {
        parent::__construct();
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

        $pluginInformation = $this->askForPluginInformation($io);

        $this->extbasePluginCreator->create($pluginInformation);

        return Command::SUCCESS;
    }

    private function askForPluginInformation(SymfonyStyle $io): PluginInformation
    {
        do {
            $extensionKey = $this->askForExtensionKey($io);
            $extensionPath = $this->getExtensionPath($extensionKey);
            if (!is_dir($extensionPath)) {
                $io->error('Can not access extension directory. Please check extension key. Extension path: ' . $extensionPath);
                $validExtensionPath = false;
            } else {
                $validExtensionPath = true;
            }
        } while (!$validExtensionPath);

        $isExtbasePlugin = $io->confirm('Do you prefer to create an extbase based plugin?');
        if ($isExtbasePlugin) {
            $extensionName = (string)$io->ask(
                'Please provide the name of your extension',
                GeneralUtility::underscoredToUpperCamelCase($extensionKey),
            );
            $pluginLabel = (string)$io->ask(
                'Please provide a label for your plugin. You will see the label in the backend.',
            );
            $pluginName = (string)$io->ask(
                'Please provide the name of your plugin. This is an internal identifier and will be used to reference your plugin in the backend.',
                GeneralUtility::underscoredToUpperCamelCase(str_replace(' ', '_', $pluginLabel)),
            );
            $pluginType = (string)$io->choice(
                'Which type of plugin you want to create. Plugins of type "plugin" you will find in tt_content column "list_type" while "content" based plugins you will find in column "CType"',
                ['plugin', 'content'],
                'plugin'
            );
        } else {
            $io->error('Implementation of plugin creation for non-extbase plugins is not yet implemented.');
            exit(1);
        }

        return new PluginInformation(
            $extensionKey,
            $isExtbasePlugin,
            $extensionName,
            $pluginLabel,
            $pluginName,
            $pluginType,
        );
    }
}
