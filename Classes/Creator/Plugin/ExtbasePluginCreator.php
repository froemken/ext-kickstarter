<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ExpressionStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;

class ExtbasePluginCreator
{
    use FileStructureBuilderTrait;

    private BuilderFactory $builderFactory;

    private NodeFactory $nodeFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
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

        /*$fileStructure->addUseStructure(new UseStructure(
            $this->builderFactory->use('TYPO3\CMS\Extbase\Utility\ExtensionUtility')->getNode()
        ));*/
        $fileStructure->addExpressionStructure(new ExpressionStructure(
            $this->getExpressionForConfigurePlugin($pluginInformation)
        ));

        file_put_contents($targetFile, $fileStructure->getFileContents());
    }

    private function getExpressionForConfigurePlugin(PluginInformation $pluginInformation): Node\Stmt\Expression
    {
        if ($pluginInformation->getPluginType() === 'plugin') {
            $pluginTypeNode = new Node\Expr\ClassConstFetch(
                new Node\Name('TYPO3\CMS\Extbase\Utility\ExtensionUtility'),
                'PLUGIN_TYPE_PLUGIN'
            );
        } else {
            $pluginTypeNode = new Node\Expr\ClassConstFetch(
                new Node\Name('TYPO3\CMS\Extbase\Utility\ExtensionUtility'),
                'PLUGIN_TYPE_CONTENT_ELEMENT'
            );
        }

        return new Node\Stmt\Expression($this->builderFactory->staticCall(
            'ExtensionUtility',
            'configurePlugin',
            [
                $pluginInformation->getExtensionName(),
                $pluginInformation->getPluginName(),
                new Node\Expr\Array_([]),
                new Node\Expr\Array_([]),
                $pluginTypeNode,
            ]
        ));
    }
}
