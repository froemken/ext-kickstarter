<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Information;

readonly class PluginInformation
{
    public function __construct(
        private readonly ExtensionInformation $extensionInformation,
        private readonly bool $extbasePlugin,
        private readonly string $pluginLabel,
        private readonly string $pluginName,
        private readonly string $pluginType,
        private array $referencedControllerActions,
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

    public function getPluginType(): string
    {
        return $this->pluginType;
    }

    public function getReferencedControllerActions(bool $cached): array
    {
        $referencedControllerActions = [];

        foreach ($this->referencedControllerActions as $referencedExtbaseControllerClassname => $referencedControllerActionNames) {
            // Remove "Action" from action name
            $controllerActionNames = array_map(static function($controllerActionName) {
                return substr($controllerActionName, 0, -6);
            }, $referencedControllerActionNames[$cached ? 'cached' : 'uncached']);

            $referencedControllerActions[$referencedExtbaseControllerClassname] = implode(
                ', ',
                $controllerActionNames
            );
        }

        return $referencedControllerActions;
    }

    /**
     * Needed to create all "use" imports
     */
    public function getReferencedControllerNames(): array
    {
        return array_keys($this->referencedControllerActions);
    }

    public function getNamespaceForControllerName(string $controllerName): string
    {
        return $this->extensionInformation->getNamespacePrefix() . 'Controller\\' . $controllerName;
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
