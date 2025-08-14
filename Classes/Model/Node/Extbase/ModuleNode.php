<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Model\Node\Extbase;

use FriendsOfTYPO3\Kickstarter\Model\AbstractNode;

class ModuleNode extends AbstractNode
{
    public function getModuleName(): string
    {
        return $this->getProperties()['moduleName'] ?? '';
    }

    /**
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
