<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Extbase;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;

class PluginNode extends AbstractNode
{
    public function getPluginName(): string
    {
        return $this->getProperties()['pluginName'] ?? '';
    }

    /**
     * This will only collect controllers/actions which have to be added in plugin configuration
     * To retrieve all controllers you should use ExtensionNode::getExtbaseControllerNodes()
     *
     * @return \SplObjectStorage|ControllerNode[]
     */
    public function getControllerNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName(
            $this,
            'useExtbaseControllers',
            'Extbase/Controller'
        );
    }

    /**
     * @return \SplObjectStorage|OverwritePluginControllerActionMappingNode[]
     */
    public function getOverwritePluginControllerActionMappings(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName(
            $this,
            'useExtbaseControllers',
            'Extbase/OverwritePluginControllerActionMapping'
        );
    }

    private function containsController(ControllerNode $controllerNode): bool
    {
        return $this->getControllerNodes()->contains($controllerNode);
    }

    /**
     * Returns all "Controller::class -> 'index, update, edit'" strings as array
     */
    public function getControllerActionDefinitionStrings(bool $isUncached): array
    {
        $definitionStrings = [];
        foreach ($this->graph->getExtensionNode()->getExtbaseControllerNodes() as $controllerNode) {
            // Add controller and ALL controller actions to plugin configuration
            $controllerName = $controllerNode->getControllerName();

            if ($this->containsController($controllerNode)
                && $definitionString = $controllerNode->getControllerActionDefinitionString($isUncached)
            ) {
                $definitionStrings[$controllerName] = $definitionString;
            }

            // If mapping with specific controller name is found, use individual action names
            foreach ($this->getOverwritePluginControllerActionMappings() as $mapping) {
                if ($mapping->isUncached() === $isUncached
                    && $mapping->getControllerName() === $controllerNode->getControllerName()
                ) {
                    $definitionStrings[$controllerName] = $mapping->getControllerActionDefinitionString();
                }
            }
        }

        // Because of array_push we need numbered array keys
        return array_values($definitionStrings);
    }
}
