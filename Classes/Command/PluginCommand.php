<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use PhpParser\Node;
use PhpParser\NodeFinder;
use StefanFroemken\ExtKickstarter\Creator\Extension\ExtensionCreatorInterface;
use StefanFroemken\ExtKickstarter\Creator\Plugin\ExtbasePluginCreator;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionPathTrait;
use StefanFroemken\ExtKickstarter\Traits\PhpParserStatementTrait;
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
    use PhpParserStatementTrait;

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
            $pluginName = $this->askForPluginName($io, $extensionKey, $extensionName, $pluginLabel);
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

    private function askForPluginName(
        SymfonyStyle $io,
        string $extensionKey,
        string $extensionName,
        string $pluginLabel
    ): string {
        do {
            $pluginName = (string)$io->ask(
                'Please provide the name of your plugin. This is an internal identifier and will be used to reference your plugin in the backend.',
                GeneralUtility::underscoredToUpperCamelCase(str_replace(' ', '_', $pluginLabel)),
            );
            if ($this->isPluginNameRegistered($extensionKey, $extensionName, $pluginName)) {
                $io->error('Your given plugin name is already registered in "ext_localconf.php" of your given extension with key: ' . $extensionKey);
                $validComposerPackageName = false;
            } else {
                $validComposerPackageName = true;
            }
        } while (!$validComposerPackageName);

        return $pluginName;
    }

    private function isPluginNameRegistered(string $extensionKey, string $extensionName, string $pluginName): bool
    {
        $nodeFinder = new NodeFinder();
        $file = $this->getExtensionPath($extensionKey) . 'ext_localconf.php';

        // Early return, if ext_localconf.php does not exists
        if (is_file($file) === false) {
            return false;
        }

        $statements = $this->getParserStatementsForFile($file);

        // Find all classes that extend another class
        $matchingStatements = $nodeFinder->find($statements, function(Node $node) use ($extensionName, $pluginName) {
            if ($node instanceof Node\Expr\StaticCall
                && $node->class->toString() === 'ExtensionUtility'
                && $node->name->toString() === 'configurePlugin'
            ) {
                $extensionNameArg = $node->args[0] instanceof Node\Arg && $node->args[0]->value instanceof Node\Scalar\String_
                    ? $node->args[0]->value->value
                    : '';

                $pluginNameArg = $node->args[1] instanceof Node\Arg && $node->args[1]->value instanceof Node\Scalar\String_
                    ? $node->args[1]->value->value
                    : '';

                return strtolower($extensionNameArg) === strtolower($extensionName)
                    && strtolower($pluginNameArg) === strtolower($pluginName);
            }

            return false;
        });

        return $matchingStatements !== [];
    }
}
