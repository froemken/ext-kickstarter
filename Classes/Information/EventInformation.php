<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

use FriendsOfTYPO3\Kickstarter\Information\Normalization\EventClassNameNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Normalization\UseNormalizer;
use FriendsOfTYPO3\Kickstarter\Information\Validatation\EventClassValidator;
use FriendsOfTYPO3\Kickstarter\Information\Validatation\UseValidator;

class EventInformation implements InformationInterface
{
    private const EVENT_PATH = 'Classes/Event/';

    private ?ExtensionInformation $extensionInformation = null;
    #[UseValidator(EventClassValidator::class)]
    #[UseNormalizer(EventClassNameNormalizer::class)]
    private ?string $eventClassName = null;

    private CreatorInformation $creatorInformation;

    public function __construct(
        ?ExtensionInformation $extensionInformation = null,
        ?string $eventClassName = null,
    ) {
        $this->creatorInformation = new CreatorInformation();
        $this->extensionInformation = $extensionInformation;
        $this->eventClassName = $eventClassName;
    }

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getEventClassName(): string
    {
        return $this->eventClassName;
    }

    public function getEventFilename(): string
    {
        return $this->eventClassName . '.php';
    }

    public function getEventFilePath(): string
    {
        return $this->getEventPath() . $this->getEventFilename();
    }

    public function getEventPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::EVENT_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Event';
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function setExtensionInformation(ExtensionInformation $extensionInformation): void
    {
        $this->extensionInformation = $extensionInformation;
    }

    public function setEventClassName(string $eventClassName): void
    {
        $this->eventClassName = $eventClassName;
    }

    public function setCreatorInformation(CreatorInformation $creatorInformation): void
    {
        $this->creatorInformation = $creatorInformation;
    }

}
