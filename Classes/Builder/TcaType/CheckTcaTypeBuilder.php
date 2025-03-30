<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder\TcaType;

use StefanFroemken\ExtKickstarter\Model\Node\Tca\SelectItemNode;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\Type\CheckNode;

class CheckTcaTypeBuilder
{
    public function getFileContent(CheckNode $checkNode): string
    {
        $selectItems = $checkNode->getSelectItems();
        if ($selectItems->count() === 0) {
            return '';
        }

        /** @var SelectItemNode $selectItem */
        $selectItem = $selectItems->current();

        return str_replace(
            [
                '{{COLUMN_NAME}}',
                '{{COLUMN_LABEL}}',
                '{{COLUMN_TYPE}}',
                '{{ITEM_LABEL}}',
                '{{ITEM_VALUE}}',
            ],
            [
                $checkNode->getColumnName(),
                $checkNode->getLabel(),
                $checkNode->getColumnType(),
                $selectItem->getLabel(),
                $selectItem->getValue(),
            ],
            $this->getTemplate()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
'{{COLUMN_NAME}}' => [
    'exclude' => true,
    'label' => '{{COLUMN_LABEL}}',
    'config' => [
        'type' => '{{COLUMN_TYPE}}',
        'items' => [
            [
                'label' => '{{ITEM_LABEL}}',
                'value' => '{{ITEM_VALUE}}',
            ],
        ],
    ],
],
EOT;
    }

}
