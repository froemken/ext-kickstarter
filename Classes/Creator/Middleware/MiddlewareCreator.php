<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Middleware;

use PhpParser\BuilderFactory;
use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\MiddleWareInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\MethodStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MiddlewareCreator implements MiddlewareCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(
        NodeFactory $nodeFactory,
        private readonly FileManager $fileManager,
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(MiddlewareInformation $middlewareInformation): void
    {
        GeneralUtility::mkdir_deep($middlewareInformation->getPath());

        $eventFilePath = $middlewareInformation->getFilePath();
        $fileStructure = $this->buildFileStructure($eventFilePath);

        if (is_file($eventFilePath)) {
            $middlewareInformation->getCreatorInformation()->fileExists(
                $eventFilePath,
                sprintf(
                    'Middleware classes can only be created, not modified. The file %s already exists and cannot be overridden. ',
                    $middlewareInformation->getFilename()
                )
            );
            return;
        }
        $this->addClassNodes($fileStructure, $middlewareInformation);
        $this->fileManager->createFile($eventFilePath, $fileStructure->getFileContents(), $middlewareInformation->getCreatorInformation());
    }

    private function addClassNodes(FileStructure $fileStructure, MiddlewareInformation $middlewareInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $middlewareInformation->getNamespace(),
                $middlewareInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addMethodStructure(
            new MethodStructure($this->nodeFactory->createMiddlewareProcessMethod())
        );

        $fileStructure->addClassStructure(
            new ClassStructure($this->nodeFactory->createMiddlewareClass($middlewareInformation->getClassName()))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('Psr\Http\Message\ResponseInterface'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('Psr\Http\Server\RequestHandlerInterface'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('Psr\Http\Server\MiddlewareInterface'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('Psr\Http\Message\ServerRequestInterface'))
        );
    }
}
