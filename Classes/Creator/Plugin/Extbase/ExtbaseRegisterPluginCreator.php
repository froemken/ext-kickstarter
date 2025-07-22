<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin\Extbase;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Arg;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeFinder;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ExpressionStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtbaseRegisterPluginCreator implements ExtbasePluginCreatorInterface
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
        $overridesPath = sprintf(
            '/%s/%s/',
            trim($pluginInformation->getExtensionInformation()->getExtensionPath(), '/'),
            'Configuration/TCA/Overrides',
        );
        GeneralUtility::mkdir_deep($overridesPath);

        $targetFile = $overridesPath . 'tt_content.php';
        $fileStructure = $this->buildFileStructure($targetFile);

        if (!is_file($targetFile)) {
            $fileStructure->addDeclareStructure(new DeclareStructure($this->nodeFactory->createDeclareStrictTypes()));
        }

        $fileStructure->addUseStructure(new UseStructure(
            $this->builderFactory->use('TYPO3\CMS\Extbase\Utility\ExtensionUtility')->getNode()
        ));

        if ($this->getStaticCallForRegisterPlugin($fileStructure, $pluginInformation) === null) {
            $fileStructure->addExpressionStructure(new ExpressionStructure(
                $this->getExpressionForRegisterPlugin($pluginInformation)
            ));
        }

        file_put_contents($targetFile, $fileStructure->getFileContents());
    }

    private function getStaticCallForRegisterPlugin(
        FileStructure $fileStructure,
        PluginInformation $pluginInformation
    ): ?StaticCall {
        $nodeFinder = new NodeFinder();
        $matchedNode = $nodeFinder->findFirst($fileStructure->getExpressionStructures()->getStmts(), static function (Node $node) use ($pluginInformation): bool {
            return $node instanceof StaticCall
                && $node->class->toString() === 'ExtensionUtility'
                && $node->name->toString() === 'registerPlugin'
                && isset($node->args[0], $node->args[1])
                && $node->args[0] instanceof Arg
                && ($extensionNameNode = $node->args[0])
                && $extensionNameNode->value instanceof String_
                && $extensionNameNode->value->value === $pluginInformation->getExtensionInformation()->getExtensionName()
                && ($pluginNameNode = $node->args[1])
                && $pluginNameNode->value instanceof String_
                && $pluginNameNode->value->value === $pluginInformation->getPluginName();
        });

        return $matchedNode instanceof StaticCall ? $matchedNode : null;
    }

    private function getExpressionForRegisterPlugin(PluginInformation $pluginInformation): Expression
    {
        return new Expression($this->builderFactory->staticCall(
            'ExtensionUtility',
            'registerPlugin',
            [
                $pluginInformation->getExtensionInformation()->getExtensionName(),
                $pluginInformation->getPluginName(),
                $pluginInformation->getPluginLabel(),
                $pluginInformation->getPluginIconIdentifier(),
                'plugins',
                $pluginInformation->getPluginDescription(),
            ]
        ));
    }
}
