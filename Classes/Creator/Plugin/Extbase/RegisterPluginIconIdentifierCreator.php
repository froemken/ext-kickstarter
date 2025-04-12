<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Plugin\Extbase;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeFinder;
use StefanFroemken\ExtKickstarter\Information\PluginInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ReturnStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;

class RegisterPluginIconIdentifierCreator implements ExtbasePluginCreatorInterface
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
        $targetFile = $pluginInformation->getConfigurationPath() . 'Icons.php';
        $fileStructure = $this->buildFileStructure($targetFile);

        if (!is_file($targetFile)) {
            $fileStructure->addReturnStructure(new ReturnStructure(new Node\Stmt\Return_(new Node\Expr\Array_())));
        }

        $fileStructure->addUseStructure(new UseStructure(
            $this->builderFactory->use('TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider')->getNode()
        ));

        if (!$this->hasArrayItemWithPluginIcon($fileStructure, $pluginInformation)) {
            $returnNode = $this->getReturnNode($fileStructure);
            if (($arrayNode = $returnNode->expr)
                && $arrayNode instanceof Node\Expr\Array_
            ) {
                $arrayNode->items = $this->getNewPluginIcons($arrayNode, $pluginInformation)->items;
            }
        }

        file_put_contents($targetFile, $fileStructure->getFileContents());
    }

    private function getNewPluginIcons(Node\Expr\Array_ $existingIcons, PluginInformation $pluginInformation): Node\Expr\Array_
    {
        $existingIcons->items[] = new Node\Expr\ArrayItem(
            $this->builderFactory->val([
                'provider' => $this->builderFactory->classConstFetch('SvgIconProvider', 'class'),
                'source' => 'EXT:' . $pluginInformation->getExtensionInformation()->getExtensionKey() . '/Resources/Public/Icons/Plugin.svg',
            ]),
            $this->builderFactory->val($pluginInformation->getPluginIconIdentifier())
        );

        return $existingIcons;
    }

    private function getReturnNode(FileStructure $fileStructure): ?Node\Stmt\Return_
    {
        $nodeFinder = new NodeFinder();
        $returnNode = $nodeFinder->findFirst($fileStructure->getReturnStructures()->getStmts(), static function (Node $node): bool {
            return $node instanceof Node\Stmt\Return_;
        });

        return $returnNode instanceof Node\Stmt\Return_ ? $returnNode : null;
    }

    private function hasArrayItemWithPluginIcon(FileStructure $fileStructure, PluginInformation $pluginInformation): bool
    {
        $nodeFinder = new NodeFinder();
        $iconIdentifierNode = $nodeFinder->findFirst($fileStructure->getReturnStructures()->getStmts(), static function (Node $node) use ($pluginInformation): bool {
            return $node instanceof Node\Expr\ArrayItem
                && $node->key instanceof Node\Scalar\String_
                && $node->key->value === $pluginInformation->getPluginIconIdentifier();
        });

        return $iconIdentifierNode instanceof Node\Expr\ArrayItem;
    }
}
