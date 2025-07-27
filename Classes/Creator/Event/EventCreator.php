<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Event;

use PhpParser\BuilderFactory;
use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\EventInformation;
use StefanFroemken\ExtKickstarter\PhpParser\NodeFactory;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ClassStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\DeclareStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\NamespaceStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EventCreator implements EventCreatorInterface
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

    public function create(EventInformation $eventInformation): void
    {
        GeneralUtility::mkdir_deep($eventInformation->getEventPath());

        $eventFilePath = $eventInformation->getEventFilePath();
        $fileStructure = $this->buildFileStructure($eventFilePath);

        if (is_file($eventFilePath)) {
            $eventInformation->getCreatorInformation()->fileExists(
                $eventFilePath,
                sprintf(
                    'Events can only be created, not modified. The file %s already exists and cannot be overridden. ',
                    $eventInformation->getEventFilename()
                )
            );
            return;
        }
        $this->addClassNodes($fileStructure, $eventInformation);
        $this->fileManager->createFile($eventFilePath, $fileStructure->getFileContents(), $eventInformation->getCreatorInformation());
    }

    private function addClassNodes(FileStructure $fileStructure, EventInformation $eventInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $eventInformation->getNamespace(),
                $eventInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure(
                $this->builderFactory
                    ->class($eventInformation->getEventClassName())
                    ->makeFinal()
                    ->getNode(),
            )
        );
    }
}
