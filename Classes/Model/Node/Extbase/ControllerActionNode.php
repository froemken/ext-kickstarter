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

class ControllerActionNode extends AbstractNode
{
    public function getActionName(): string
    {
        return $this->getProperties()['actionName'] ?? '';
    }

    public function isUncached(): bool
    {
        return (bool)($this->getProperties()['uncached'] ?? false);
    }
}
