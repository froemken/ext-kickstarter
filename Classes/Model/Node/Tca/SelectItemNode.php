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

class SelectItemNode extends AbstractNode
{
    public function getLabel(): string
    {
        return $this->getProperties()['label'] ?? '';
    }

    public function getValue(): string
    {
        return $this->getProperties()['value'] ?? '';
    }

    public function asArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'value' => $this->getValue(),
        ];
    }
}
