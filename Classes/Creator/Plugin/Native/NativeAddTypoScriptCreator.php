<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin\Native;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Expression;
use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ExpressionStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;

class NativeAddTypoScriptCreator implements NativePluginCreatorInterface
{
    use FileStructureBuilderTrait;

    private BuilderFactory $builderFactory;

    private NodeFactory $nodeFactory;

    public function __construct(
        NodeFactory $nodeFactory,
        private readonly FileManager $fileManager,
    ) {
        $this->builderFactory = new BuilderFactory();
        $this->nodeFactory = $nodeFactory;
    }

    public function create(PluginInformation $pluginInformation): void
    {
        $targetFile = $pluginInformation->getExtensionInformation()->getExtensionPath() . 'ext_localconf.php';
        $fileStructure = $this->buildFileStructure($targetFile);

        if (!is_file($targetFile)) {
            $fileStructure->addDeclareStructure(new DeclareStructure($this->nodeFactory->createDeclareStrictTypes()));
        }

        $fileStructure->addUseStructure(new UseStructure(
            $this->builderFactory->use('TYPO3\CMS\Core\Utility\ExtensionManagementUtility')->getNode()
        ));
        $fileStructure->addExpressionStructure(new ExpressionStructure(
            $this->getExpressionForAddTypoScript($pluginInformation)
        ));

        $this->fileManager->createOrModifyFile($targetFile, $fileStructure->getFileContents(), $pluginInformation->getCreatorInformation());
    }

    private function getExpressionForAddTypoScript(PluginInformation $pluginInformation): Expression
    {
        $typoScriptSetup = str_replace(
            [
                '{PLUGIN_NAMESPACE}',
                '{EXTENSION_NAMESPACE}',
            ],
            [
                $pluginInformation->getPluginNamespace(),
                $pluginInformation->getExtensionInformation()->getNamespacePrefix(),
            ],
            $this->getTypoScriptSetup(),
        );

        return new Expression($this->builderFactory->staticCall(
            'ExtensionManagementUtility',
            'addTypoScript',
            [
                $pluginInformation->getExtensionInformation()->getExtensionKey(),
                'setup',
                $typoScriptSetup,
                'defaultContentRendering',
            ]
        ));
    }

    private function getTypoScriptSetup(): string
    {
        return <<<'EOT'
plugin.tx_{PLUGIN_NAMESPACE} = USER
plugin.tx_{PLUGIN_NAMESPACE} {
  userFunc = {EXTENSION_NAMESPACE}Controller\MyController->doSomething
}

tt_content.{PLUGIN_NAMESPACE} < plugin.tx_{PLUGIN_NAMESPACE}
EOT;
    }
}
