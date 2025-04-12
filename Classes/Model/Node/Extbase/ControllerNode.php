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

class ControllerNode extends AbstractNode
{
    public function getControllerName(): string
    {
        return $this->getProperties()['controllerName'] ?? '';
    }

    public function getControllerFilename(): string
    {
        return $this->getControllerName() . '.php';
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

    public function getModelName(): string
    {
        return substr($this->getControllerName(), 0, -10);
    }

    public function getNamespace(): string
    {
        return sprintf(
            '%s\\%s',
            $this->graph->getExtensionNode()->getNamespacePrefix(),
            'Controller'
        );
    }

    /**
     * @return \SplObjectStorage|ControllerActionNode[]
     */
    public function getControllerActionNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'extbaseControllerActions');
    }

    /**
     * @return \SplObjectStorage|RepositoryNode[]
     */
    public function getRepositoryNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'extbaseRepositories');
    }

    /**
     * @return \SplObjectStorage|ControllerActionNode[]
     */
    public function getUncachedControllerActionNodes(): \SplObjectStorage
    {
        $cachedControllerActions = new \SplObjectStorage();

        foreach ($this->getControllerActionNodes() as $controllerAction) {
            if ($controllerAction->isUncached()) {
                $cachedControllerActions->attach($controllerAction);
            }
        }

        return $cachedControllerActions;
    }
}
