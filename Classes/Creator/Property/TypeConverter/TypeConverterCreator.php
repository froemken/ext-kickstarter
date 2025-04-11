<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Property\TypeConverter;

use PhpParser\BuilderFactory;
use StefanFroemken\ExtKickstarter\Information\TypeConverterInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\MethodStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypeConverterCreator implements TypeConverterCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(TypeConverterInformation $typeConverterInformation): void
    {
        GeneralUtility::mkdir_deep($typeConverterInformation->getTypeConverterPath());

        $typeConverterFilePath = $typeConverterInformation->getTypeConverterFilePath();
        $fileStructure = $this->buildFileStructure($typeConverterFilePath);

        if (!is_file($typeConverterFilePath)) {
            $this->addClassNodes($fileStructure, $typeConverterInformation);
            file_put_contents($typeConverterFilePath, $fileStructure->getFileContents());
        }
    }

    private function addClassNodes(FileStructure $fileStructure, TypeConverterInformation $typeConverterInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;'))
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $typeConverterInformation->getNamespace(),
                $typeConverterInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure(
                $this->builderFactory
                    ->class($typeConverterInformation->getTypeConverterClassName())
                    ->makeFinal()
                    ->extend('AbstractTypeConverter')
                    ->getNode(),
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('convertFrom')
                    ->addParam($this->builderFactory->param('source'))
                    ->addParam($this->builderFactory->param('targetType')->setType('string'))
                    ->addParam($this->builderFactory->param('convertedChildProperties')->setType('array')->setDefault([]))
                    ->addParam($this->builderFactory->param('configuration')->setType('?PropertyMappingConfigurationInterface')->setDefault(null))
                    ->makePublic()
                    ->setReturnType('void')
                    ->getNode()
            )
        );
    }
}
