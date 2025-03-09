<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Tca;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;

class TableNode extends AbstractNode
{
    public function getTableName(): string
    {
        return $this->getProperties()['tableName'] ?? '';
    }

    public function getTableFilename(): string
    {
        return $this->getTableName() . '.php';
    }

    public function getTableTitle(): string
    {
        return $this->getProperties()['title'] ?? '';
    }

    /**
     * @return \SplObjectStorage|AbstractColumnNode[]
     */
    public function getColumnNodes(): \SplObjectStorage
    {
        return $this->graph->getLinkedOutputNodesByName($this, 'tcaColumns');
    }
}
