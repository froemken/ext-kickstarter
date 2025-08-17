<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\Event;

use FriendsOfTYPO3\Kickstarter\Creator\FileManager;
use FriendsOfTYPO3\Kickstarter\Information\EventInformation;
use FriendsOfTYPO3\Kickstarter\Information\ExtensionInformation;
use FriendsOfTYPO3\Kickstarter\Parser\ExtensionInformationParser;
use FriendsOfTYPO3\Kickstarter\PhpParser\NodeFactory;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\ClassStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\DeclareStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\FileStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\NamespaceStructure;
use FriendsOfTYPO3\Kickstarter\Traits\FileStructureBuilderTrait;
use PhpParser\BuilderFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EventCreator implements EventCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(
        NodeFactory $nodeFactory,
        private readonly FileManager $fileManager,
        private ExtensionInformationParser $extensionInformationParser,
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(EventInformation $eventInformation): void
    {
        $extensionInformation = $this->extensionInformationParser->parse($eventInformation->getExtensionInformation());
        GeneralUtility::mkdir_deep($extensionInformation->getEventPath());

        $eventFilePath = $extensionInformation->getEventPath() . $eventInformation->getEventFilename();
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
        $this->addClassNodes($fileStructure, $eventInformation, $extensionInformation);
        $this->fileManager->createFile($eventFilePath, $fileStructure->getFileContents(), $eventInformation->getCreatorInformation());
    }

    private function addClassNodes(FileStructure $fileStructure, EventInformation $eventInformation, ExtensionInformation $extensionInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $extensionInformation->getEventNamespace(),
                $extensionInformation,
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
