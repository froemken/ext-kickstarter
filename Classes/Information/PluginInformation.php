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
        private readonly string $pluginLabel,
        private readonly string $pluginName,
    ) {}

    public function getExtensionInformation(): ExtensionInformation
    {
        return $this->extensionInformation;
    }

    public function isExtbasePlugin(): bool
    {
        return $this->extbasePlugin;
    }

    public function getPluginLabel(): string
    {
        return $this->pluginLabel;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * Needed for core native plugins
     */
    public function getPluginNamespace(): string
    {
        return sprintf(
            '%s_%s',
            str_replace('_', '', $this->extensionInformation->getExtensionKey()),
            strtolower($this->pluginName),
        );
    }
}
