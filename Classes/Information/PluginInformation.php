<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

class PluginInformation
{
    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly bool $extbasePlugin,
        private readonly string $extensionName,
        private readonly string $pluginLabel,
        private readonly string $pluginName,
        private readonly string $pluginType,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function isExtbasePlugin(): bool
    {
        return $this->extbasePlugin;
    }

    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function getPluginLabel(): string
    {
        return $this->pluginLabel;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getPluginType(): string
    {
        return $this->pluginType;
    }
}
