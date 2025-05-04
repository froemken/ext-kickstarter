<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class EventListenerInformation
{
    private const EVENT_LISTENER_PATH = 'Classes/EventListener/';

    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly string $eventListenerClassName,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function getEventListenerClassName(): string
    {
        return $this->eventListenerClassName;
    }

    public function getEventListenerFilename(): string
    {
        return $this->eventListenerClassName . '.php';
    }

    public function getEventListenerFilePath(): string
    {
        return $this->getEventListenerPath() . $this->getEventListenerFilename();
    }

    public function getEventListenerPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::EVENT_LISTENER_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'EventListener';
    }
}
