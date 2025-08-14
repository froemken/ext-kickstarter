<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Information;

class EventInformation
{
    private const EVENT_PATH = 'Classes/Event/';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly string $eventClassName,
    ) {}

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
}
