<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Domain\Model;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Return_;
use StefanFroemken\ExtKickstarter\Information\ModelInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ReturnStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClassMapCreator implements DomainCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(ModelInformation $modelInformation): void
    {
        if ($modelInformation->isExpectedTableName()) {
            return;
        }

        GeneralUtility::mkdir_deep(dirname($modelInformation->getClassesFilePath()));

        $classesFilePath = $modelInformation->getClassesFilePath();
        $fileStructure = $this->buildFileStructure($classesFilePath);

        if (!is_file($classesFilePath)) {
            $this->addClassNodes($fileStructure);
        }

        /** @var ReturnStructure $returnStructure */
        $returnStructure = $fileStructure->getReturnStructures()->current();
        /** @var Array_ $arrayNode */
        $arrayNode = $returnStructure->getNode()->expr;

        if (!$this->classMapExists($arrayNode, $modelInformation)) {
            $fileStructure->addUseStructure(
                new UseStructure($this->nodeFactory->createUseImport(
                    $modelInformation->getNamespace() . '\\' . $modelInformation->getModelClassName()
                ))
            );

            $arrayNode->items[] = new ArrayItem(
                $this->builderFactory->val(['tableName' => $modelInformation->getMappedTableName()]),
                $this->builderFactory->classConstFetch($modelInformation->getModelClassName(), 'class'),
            );
        }

        file_put_contents($classesFilePath, $fileStructure->getFileContents());
    }

    private function classMapExists(Array_ $arrayNode, ModelInformation $modelInformation): bool
    {
        foreach ($arrayNode->items as $arrayItem) {
            if ($arrayItem->key instanceof Scalar\String_
                && $arrayItem->key->value === $modelInformation->getModelClassName()
            ) {
                return true;
            }
        }

        return false;
    }

    private function addClassNodes(FileStructure $fileStructure): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addReturnStructure(
            new ReturnStructure(
                new Return_(
                    $this->builderFactory->val([])
                )
            )
        );
    }
}
