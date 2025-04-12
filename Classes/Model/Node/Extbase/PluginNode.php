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
}
