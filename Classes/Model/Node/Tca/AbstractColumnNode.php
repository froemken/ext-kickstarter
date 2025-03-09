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

abstract class AbstractColumnNode extends AbstractNode
{
    public function getColumnName(): string
    {
        return $this->getProperties()['columnName'] ?? '';
    }

    public function getLabel(): string
    {
        return $this->getProperties()['label'] ?? '';
    }

    public function getColumnType(): string
    {
        return $this->getProperties()['tcaType'] ?? '';
    }
}
