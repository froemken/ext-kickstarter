<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Domain;

use PhpParser\BuilderFactory;
use StefanFroemken\ExtKickstarter\Information\RepositoryInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RepositoryCreator
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(RepositoryInformation $repositoryInformation): void
    {
        GeneralUtility::mkdir_deep($repositoryInformation->getRepositoryPath());

        $repositoryFilePath = $repositoryInformation->getRepositoryFilePath();
        $fileStructure = $this->buildFileStructure($repositoryFilePath);

        if (!is_file($repositoryFilePath)) {
            $this->addClassNodes($fileStructure, $repositoryInformation);
            file_put_contents($repositoryFilePath, $fileStructure->getFileContents());
        }
    }

    private function addClassNodes(FileStructure $fileStructure, RepositoryInformation $repositoryInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Extbase\Persistence\Repository'))
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $repositoryInformation->getNamespace(),
                $repositoryInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure(
                $this->builderFactory
                    ->class($repositoryInformation->getRepositoryClassName())
                    ->extend('Repository')
                    ->makeFinal()
                    ->getNode(),
            )
        );
    }
}
