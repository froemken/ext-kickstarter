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

class NativeAddPluginCreator
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

        $fileStructure->addUseStructure(new UseStructure(
            $this->builderFactory->use('TYPO3\CMS\Core\Utility\ExtensionManagementUtility')->getNode()
        ));
        $fileStructure->addExpressionStructure(new ExpressionStructure(
            $this->getExpressionForAddPlugin($pluginInformation)
        ));

        file_put_contents($targetFile, $fileStructure->getFileContents());
    }

    private function getExpressionForAddPlugin(PluginInformation $pluginInformation): Node\Stmt\Expression
    {
        $pluginType = 'list_type';
        if ($pluginInformation->getPluginType() === 'content') {
            $pluginType = 'CType';
        }

        $pluginIconPath = sprintf(
            'EXT:%s/Resources/Public/Icons/Extension.svg',
            $pluginInformation->getExtensionInformation()->getExtensionKey(),
        );

        // Seems to be the group within the CType selector in TYPO3 backend
        // As that grouping is only valid for CType based plugins
        // I change group only for that specific type
        $group = 'default';
        if ($pluginInformation->getPluginType() === 'content') {
            $group = 'plugins';
        }

        return new Node\Stmt\Expression($this->builderFactory->staticCall(
            'ExtensionManagementUtility',
            'addPlugin',
            [
                [
                    'label' => $pluginInformation->getPluginLabel(),
                    'value' => $pluginInformation->getPluginNamespace(),
                    'group' => $group,
                    'icon' => $pluginIconPath,
                    'description' => 'Please update the description',
                ],
                $pluginType,
                $pluginInformation->getExtensionInformation()->getExtensionKey(),
            ]
        ));
    }
}
