<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder\PropertyType;

use StefanFroemken\ExtKickstarter\Model\Node\Tca\AbstractColumnNode;

class DefaultPropertyTypeBuilder
{
    public function getFileContent(AbstractColumnNode $columnNode): string
    {
        return str_replace(
            [
                '{{DATA_TYPE}}',
                '{{LC_PROPERTY}}',
                '{{DEFAULT}}',
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
protected {{DATA_TYPE}} ${{LC_PROPERTY}} = {{DEFAULT}};
EOT;
    }
}