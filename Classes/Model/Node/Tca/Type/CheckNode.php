<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Tca\Type;

use StefanFroemken\ExtKickstarter\Model\Node\Tca\AbstractColumnNode;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\SelectItemNode;

class CheckNode extends AbstractColumnNode
{
    public const TYPE = 'check';

    /**
     * @return \SplObjectStorage|SelectItemNode[]
     */
    public function getSelectItems(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'tcaSelectItems');
    }
}
