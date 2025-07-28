<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

readonly class ControllerInformation
{
    private const CONTROLLER_PATH = 'Classes/Controller/';

    public function __construct(
        private ExtensionInformation $extensionInformation,
        private bool $isExtbaseController,
        private string $controllerName,
        private array $actionMethodNames,
        private CreatorInformation $creatorInformation = new CreatorInformation()
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function isExtbaseController(): bool
    {
        return $this->isExtbaseController;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getControllerFilename(): string
    {
        return $this->controllerName . '.php';
    }

    public function getControllerFilePath(): string
    {
        return $this->getControllerPath() . $this->getControllerFilename();
    }

    public function getControllerPath(): string
    {
        return $this->extensionInformation->getExtensionPath() . self::CONTROLLER_PATH;
    }

    public function getNamespace(): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Controller';
    }

    public function getActionMethodNames(): array
    {
        return $this->actionMethodNames;
    }

    public function getCreatorInformation(): CreatorInformation
    {
        return $this->creatorInformation;
    }
}
