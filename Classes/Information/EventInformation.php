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

class EventInformation implements ExtensionRelatedInformationInterface
{
    private ?ExtensionMappingInformation $extensionInformation = null;

    #[UseValidator(EventClassValidator::class)]
    #[UseNormalizer(EventClassNameNormalizer::class)]
    private ?string $eventClassName = null;

    private CreatorInformation $creatorInformation;

    public function __construct(
        ?ExtensionMappingInformation $extensionInformation = null,
        ?string $eventClassName = null,
    ) {
        $this->creatorInformation = new CreatorInformation();
        $this->extensionInformation = $extensionInformation;
        $this->eventClassName = $eventClassName;
    }

    public function getExtensionInformation(): ?ExtensionMappingInformation
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

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }

    public function setExtensionInformation(ExtensionMappingInformation $extensionMappingInformation): void
    {
        $this->extensionInformation = $extensionMappingInformation;
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
