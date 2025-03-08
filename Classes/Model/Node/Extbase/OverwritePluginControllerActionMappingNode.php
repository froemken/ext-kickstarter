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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OverwritePluginControllerActionMappingNode extends AbstractNode
{
    public function getControllerName(): string
    {
        return $this->getProperties()['controllerName'] ?? '';
    }

    public function getControllerClass(): string
    {
        return sprintf(
            '%s\\%s\\%s',
            $this->graph->getExtensionNode()->getClassnamePrefix(),
            'Controller',
            $this->getControllerName(),
        );
    }

    public function getActionNames(): array
    {
        return GeneralUtility::trimExplode(
            ',',
            ($this->getProperties()['actionNames'] ?? ''),
            true
        );
    }

    public function isUncached(): bool
    {
        return (bool)($this->getProperties()['uncached'] ?? false);
    }

    public function getShortActionNames(): array
    {
        $shortActionNames = [];

        foreach ($this->getActionNames() as $actionName) {
            if (str_ends_with($actionName, 'Action')) {
                $actionName = substr($actionName, 0, -6);
            }
            $shortActionNames[] = $actionName;
        }

        return $shortActionNames;
    }

    /**
     * Returns the "Controller::class -> 'index, update, edit'" string
     */
    public function getControllerActionDefinitionString(): string
    {
        return sprintf(
            '%s::class => \'%s\',',
            $this->getControllerClass(),
            implode(',', $this->getShortActionNames())
        );
    }
}
