<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class ControllerInformation
{
    public function __construct(
        private readonly string $extensionKey,
        private readonly bool $isExtbaseController,
        private readonly string $controllerName,
        private readonly array $actionMethodNames,
    ) {}

    public function getExtensionKey(): string
    {
        return $this->extensionKey;
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

    public function getActionMethodNames(): array
    {
        return $this->actionMethodNames;
    }
}