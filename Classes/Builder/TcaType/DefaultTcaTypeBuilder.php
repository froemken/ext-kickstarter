<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder\TcaType;

use StefanFroemken\ExtKickstarter\Model\Node\Tca\AbstractColumnNode;

class DefaultTcaTypeBuilder
{
    public function getFileContent(AbstractColumnNode $columnNode): string
    {
        return str_replace(
            [
                '{{COLUMN_NAME}}',
                '{{COLUMN_LABEL}}',
                '{{COLUMN_TYPE}}',
            ],
            [
                $columnNode->getColumnName(),
                $columnNode->getLabel(),
                $columnNode->getColumnType(),
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
    ],
],
EOT;
    }

}