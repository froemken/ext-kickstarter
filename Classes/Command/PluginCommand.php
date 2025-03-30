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
use StefanFroemken\ExtKickstarter\Creator\Plugin\ExtbaseConfigurePluginCreator;
use StefanFroemken\ExtKickstarter\Creator\Plugin\ExtbaseRegisterPluginCreator;
use StefanFroemken\ExtKickstarter\Creator\Plugin\NativeAddPluginCreator;
use StefanFroemken\ExtKickstarter\Creator\Plugin\NativeAddTypoScriptCreator;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
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
        private readonly ExtbaseConfigurePluginCreator $extbaseConfigurePluginCreator,
        private readonly ExtbaseRegisterPluginCreator $extbaseRegisterPluginCreator,
        private readonly NativeAddPluginCreator $nativeAddPluginCreator,
        private readonly NativeAddTypoScriptCreator $nativeAddTypoScriptCreator,
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

        $pluginInformation = $this->askForPluginInformation($io, $input);

        if ($pluginInformation->isExtbasePlugin()) {
            $this->extbaseConfigurePluginCreator->create($pluginInformation);
            $this->extbaseRegisterPluginCreator->create($pluginInformation);
        } else {
            $this->nativeAddPluginCreator->create($pluginInformation);
            $this->nativeAddTypoScriptCreator->create($pluginInformation);
        }

        return Command::SUCCESS;
    }

    private function askForPluginInformation(SymfonyStyle $io, InputInterface $input): PluginInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        $isExtbasePlugin = $io->confirm('Do you prefer to create an extbase based plugin?');

        $pluginLabel = (string)$io->ask(
            'Please provide a label for your plugin. You will see the label in the backend.',
        );
        $pluginName = $this->askForPluginName($io, $extensionInformation, $pluginLabel);
        $pluginType = (string)$io->choice(
            'Which type of plugin you want to create. Plugins of type "plugin" you will find in tt_content column "list_type" while "content" based plugins you will find in column "CType"',
            ['plugin', 'content'],
            'plugin'
        );

        return new PluginInformation(
            $extensionInformation,
            $isExtbasePlugin,
            $pluginLabel,
            $pluginName,
            $pluginType,
        );
    }

    private function askForPluginName(
        SymfonyStyle $io,
        ExtensionInformation $extensionInformation,
        string $pluginLabel,
    ): string {
        do {
            $pluginName = (string)$io->ask(
                'Please provide the name of your plugin. This is an internal identifier and will be used to reference your plugin in the backend.',
                GeneralUtility::underscoredToUpperCamelCase(str_replace(' ', '_', $pluginLabel)),
            );
            if ($this->isPluginNameRegistered($extensionInformation, $pluginName)) {
                $io->error(sprintf(
                    '%s: %s',
                    'Your given plugin name is already registered in "ext_localconf.php" of your given extension with key',
                    $extensionInformation->getExtensionKey()
                ));
                $validComposerPackageName = false;
            } else {
                $validComposerPackageName = true;
            }
        } while (!$validComposerPackageName);

        return $pluginName;
    }

    private function isPluginNameRegistered(ExtensionInformation $extensionInformation, string $pluginName): bool
    {
        $nodeFinder = new NodeFinder();
        $file = $extensionInformation->getExtensionPath() . 'ext_localconf.php';
        $extensionName = $extensionInformation->getExtensionName();

        // Early return, if ext_localconf.php does not exist
        if (is_file($file) === false) {
            return false;
        }

        $fileStructure = $this->buildFileStructure($file);

        // Find all classes that extend another class
        $matchingStatements = $nodeFinder->find($fileStructure->getExpressionStructures()->getStmts(), function (Node $node) use ($extensionName, $pluginName) {
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
