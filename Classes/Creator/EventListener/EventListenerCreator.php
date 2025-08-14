<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Creator\EventListener;

use PhpParser\BuilderFactory;
use FriendsOfTYPO3\Kickstarter\Information\EventListenerInformation;
use FriendsOfTYPO3\Kickstarter\PhpParser\NodeFactory;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\ClassStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\DeclareStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\FileStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\MethodStructure;
use FriendsOfTYPO3\Kickstarter\PhpParser\Structure\NamespaceStructure;
use FriendsOfTYPO3\Kickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EventListenerCreator implements EventListenerCreatorInterface
{
    use FileStructureBuilderTrait;

    private NodeFactory $nodeFactory;

    private BuilderFactory $builderFactory;

    public function __construct(NodeFactory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
        $this->builderFactory = new BuilderFactory();
    }

    public function create(EventListenerInformation $eventListenerInformation): void
    {
        GeneralUtility::mkdir_deep($eventListenerInformation->getEventListenerPath());

        $eventListenerFilePath = $eventListenerInformation->getEventListenerFilePath();
        $fileStructure = $this->buildFileStructure($eventListenerFilePath);

        if (!is_file($eventListenerFilePath)) {
            $this->addClassNodes($fileStructure, $eventListenerInformation);
            file_put_contents($eventListenerFilePath, $fileStructure->getFileContents());
        }
    }

    private function addClassNodes(FileStructure $fileStructure, EventListenerInformation $eventListenerInformation): void
    {
        $fileStructure->addDeclareStructure(
            new DeclareStructure($this->nodeFactory->createDeclareStrictTypes())
        );
        $fileStructure->addNamespaceStructure(
            new NamespaceStructure($this->nodeFactory->createNamespace(
                $eventListenerInformation->getNamespace(),
                $eventListenerInformation->getExtensionInformation(),
            ))
        );
        $fileStructure->addClassStructure(
            new ClassStructure(
                $this->builderFactory
                    ->class($eventListenerInformation->getEventListenerClassName())
                    ->makeFinal()
                    ->getNode(),
            )
        );
        $fileStructure->addMethodStructure(
            new MethodStructure(
                $this->builderFactory
                    ->method('__invoke')
                    ->addParam($this->builderFactory->param('event')->setType('Replace\Me\Event'))
                    ->makePublic()
                    ->setReturnType('void')
                    ->getNode()
            )
        );
    }
}
