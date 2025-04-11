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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        return static::TYPE;
    }

    public function isModelProperty(): bool
    {
        return $this->getProperties()['modelProperty'] ?? false;
    }

    public function getPropertyName(): string
    {
        return $this->isModelProperty()
            ? GeneralUtility::underscoredToLowerCamelCase($this->getColumnName())
            : '';
    }

    public function getPropertyDataType(): string
    {
        return $this->isModelProperty()
            ? $this->getProperties()['propertyDataType'] ?? 'string'
            : '';
    }
}
