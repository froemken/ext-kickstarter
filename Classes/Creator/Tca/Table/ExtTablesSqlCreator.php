<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Tca\Table;

use StefanFroemken\ExtKickstarter\Information\TableInformation;
use StefanFroemken\ExtKickstarter\Service\TcaSchemaService;

class ExtTablesSqlCreator implements TcaTableCreatorInterface
{
    private TcaSchemaService $tcaSchemaService;

    public function __construct(TcaSchemaService $tcaSchemaService)
    {
        $this->tcaSchemaService = $tcaSchemaService;
    }

    public function create(TableInformation $tableInformation): void
    {
        $targetFile = $tableInformation->getExtensionInformation()->getExtensionPath() . 'ext_tables.sql';
        $tableName = $tableInformation->getTableName();
        $extTablesSqlLines = is_file($targetFile) ? file($targetFile, FILE_IGNORE_NEW_LINES) : [];
        $updatedLines = [];
        $inTable = false;
        $insertIndex = null;
        $existingColumns = [];

        if ($extTablesSqlLines === false) {
            return;
        }

        $this->addTableHeaderIfNotExists($tableName, $extTablesSqlLines);

        $columnDefinitionLines = $this->getColumnDefinitionLines($tableInformation);

        foreach ($extTablesSqlLines as $index => $extTablesSqlLine) {
            if (preg_match('/CREATE TABLE `?' . $tableName . '`?\s*\(/i', $extTablesSqlLine)) {
                $inTable = true;
            }

            if ($inTable && preg_match('/^\s*`?(\w+)`?\s+/', $extTablesSqlLine, $matches)) {
                $existingColumns[] = $matches[1];
            }

            if ($inTable && preg_match('/^(\s*)(KEY|INDEX|UNIQUE) /i', $extTablesSqlLine, $matches) && $insertIndex === null) {
                $insertIndex = $index;
            }

            if ($inTable && trim($extTablesSqlLine) === ');') {
                if ($insertIndex === null) {
                    $insertIndex = $index;
                }
                foreach ($columnDefinitionLines as $columnDefinitionLine) {
                    if (!$this->columnExists($columnDefinitionLine, $existingColumns)) {
                        $updatedLines[] = $this->formatColumnDefinition($columnDefinitionLine);
                    }
                }

                // Remove the trailing comma from the last element if it's a column definition
                if ($updatedLines !== []) {
                    $lastKey = array_key_last($updatedLines);
                    $updatedLines[$lastKey] = rtrim($updatedLines[$lastKey], ',');
                }

                $inTable = false;
            }

            $updatedLines[] = $extTablesSqlLine;
        }

        file_put_contents($targetFile, implode("\n", $updatedLines));
    }

    private function addTableHeaderIfNotExists(string $tableName, array &$extTablesSqlLines): void
    {
        if (!$this->tableExists($tableName, $extTablesSqlLines)) {
            if ($extTablesSqlLines !== [] && trim(end($extTablesSqlLines)) !== '') {
                $extTablesSqlLines[] = '';
            }
            $extTablesSqlLines[] = '#';
            $extTablesSqlLines[] = '# Table structure for table ' . $tableName;
            $extTablesSqlLines[] = '#';
            $extTablesSqlLines[] = 'CREATE TABLE ' . $tableName . ' (';
            $extTablesSqlLines[] = ');';
        }
    }

    private function getColumnDefinitionLines(TableInformation $tableInformation): array
    {
        $columnDefinitionLines = [];

        foreach ($tableInformation->getColumns() as $tableColumnName => $columnConfiguration) {
            $columnConfiguration['config']['columnName'] = $tableColumnName;
            $extTablesSqlInformation = $this->tcaSchemaService->getExtTablesSqlInformationBasedOnTca(
                $columnConfiguration['config']['type'],
                $columnConfiguration['config']
            );

            $columnDefinitionLines[] = $extTablesSqlInformation->getColumnDefinitionSql();
        }

        return $columnDefinitionLines;
    }

    private function tableExists(string $tableName, array $lines): bool
    {
        foreach ($lines as $line) {
            if (preg_match('/CREATE TABLE `?' . $tableName . '`?\s*\(/i', $line) === 1) {
                return true;
            }
        }

        return false;
    }

    private function columnExists(string $columnDefinition, array $existingColumns): bool
    {
        if (preg_match('/^\s*`?(\w+)`?\s+/', $columnDefinition, $matches)) {
            return in_array($matches[1], $existingColumns, true);
        }
        return false;
    }

    private function formatColumnDefinition(string $columnDefinition): string
    {
        return '    ' . rtrim($columnDefinition, ',') . ',';
    }
}
