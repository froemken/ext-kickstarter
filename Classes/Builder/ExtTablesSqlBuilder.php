<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder;

use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\AbstractColumnNode;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\TableNode;
use StefanFroemken\ExtKickstarter\Service\TcaSchemaService;

/**
 * Get file content for ext_tables.sql
 */
class ExtTablesSqlBuilder implements BuilderInterface
{
    public function __construct(
        private readonly TcaSchemaService $tcaSchemaService,
    ) {}

    public function build(Graph $graph, string $extPath): void
    {
        $extensionNode = $graph->getExtensionNode();
        $tableNodes = $extensionNode->getTableNodes();
        if ($tableNodes->count() === 0) {
            return;
        }

        if (!$extensionNode->hasColumnNodes()) {
            return;
        }

        file_put_contents(
            $extPath . 'ext_tables.sql',
            $this->getFileContent($tableNodes)
        );
    }

    /**
     * @param \SplObjectStorage|TableNode[] $tableNodes
     */
    private function getFileContent(\SplObjectStorage $tableNodes): string
    {
        $sqlLines = [];
        foreach ($tableNodes as $tableNode) {
            $columnNodes = $tableNode->getColumnNodes();
            if ($columnNodes->count() === 0) {
                continue;
            }

            $sqlDefinitionString = str_replace(
                [
                    '{{TABLE_NAME}}',
                    '{{COLUMNS}}',
                ],
                [
                    $tableNode->getTableName(),
                    implode(',' . chr(10), $this->getColumnLines($columnNodes)),
                ],
                $this->getTemplate()
            );

            array_push($sqlLines, ...explode(chr(10), $sqlDefinitionString));
            $sqlLines[] = '';
        }

        array_pop($sqlLines);

        return implode(chr(10), $sqlLines);
    }

    /**
     * @param \SplObjectStorage|AbstractColumnNode[] $columnNodes
     */
    private function getColumnLines(\SplObjectStorage $columnNodes): array
    {
        $columnLines = [];
        foreach ($columnNodes as $columnNode) {
            $columnInformation = $this->tcaSchemaService->getColumnInformationBasedOnTca(
                $columnNode->getColumnType(),
                $columnNode->getProperties()
            );
            if (in_array($columnInformation['columnType'], ['TEXT', 'JSON'], true)) {
                $columnLines[] = sprintf(
                    '  %s %s %s',
                    $columnInformation['columnName'],
                    $columnInformation['unsigned'] ?? '',
                    $columnInformation['columnType'],
                );
            } else {
                $default = $columnInformation['default'] ?? '';
                if ($default === '') {
                    $default = '\'\'';
                }

                $columnLines[] = sprintf(
                    '  %s %s %s%s DEFAULT %s %s',
                    $columnInformation['columnName'],
                    $columnInformation['unsigned'] ? 'unsigned' : '',
                    $columnInformation['columnType'],
                    $columnInformation['length'] ? '(' . $columnInformation['length'] . ')' : '',
                    $default,
                    $columnInformation['notnull'] ? 'NOT NULL' : 'NULL',
                );
            }
        }

        return $columnLines;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
#
# Table structure for table '{{TABLE_NAME}}'
#
CREATE TABLE {{TABLE_NAME}}
(
{{COLUMNS}}
);
EOT;
    }
}
