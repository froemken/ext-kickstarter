<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\Event;

use PhpParser\BuilderFactory;
use FriendsOfTYPO3\Kickstarter\Information\EventInformation;
use FriendsOfTYPO3\Kickstarter\PhpParser\NodeFactory;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\ClassStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\DeclareStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\FileStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\NamespaceStructure;
use FriendsOfTYPO3\Kickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EventCreator implements EventCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(EventInformation $eventInformation): void
    {
        GeneralUtility::mkdir_deep($eventInformation->getEventPath());

        $eventFilePath = $eventInformation->getEventFilePath();
        $fileStructure = $this->buildFileStructure($eventFilePath);

        if (!is_file($eventFilePath)) {
            $this->addClassNodes($fileStructure, $eventInformation);
            file_put_contents($eventFilePath, $fileStructure->getFileContents());
        }
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
