<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Controller;

use StefanFroemken\ExtKickstarter\Information\ControllerInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\MethodStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\UseStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ControllerCreator
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    public function create(ControllerInformation $controllerInformation): void
    {
        GeneralUtility::mkdir_deep($controllerInformation->getControllerPath());

        $controllerFile = $controllerInformation->getControllerFilePath();
        $fileStructure = $this->buildFileStructure($controllerFile);

        // Add all methods. "->add*" will NOT overwrite existing action methods
        foreach ($controllerInformation->getActionMethodNames() as $actionMethodName) {
            $fileStructure->addMethodStructure(
                new MethodStructure($this->nodeFactory->createControllerActionMethod($actionMethodName))
            );
        }

        if (!is_file($controllerFile)) {
            $this->addClassNodes($fileStructure, $controllerInformation);
        }

        file_put_contents($controllerFile, $fileStructure->getFileContents());
    }

    private function addClassNodes(FileStructure $fileStructure, ControllerInformation $controllerInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $controllerInformation->getNamespace(),
                $controllerInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure($this->nodeFactory->createClass($controllerInformation->getControllerName()))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('Psr\Http\Message\ResponseInterface'))
        );
        $fileStructure->addUseStructure(
            new UseStructure($this->nodeFactory->createUseImport('TYPO3\CMS\Core\Http\HtmlResponse'))
        );
    }
}
