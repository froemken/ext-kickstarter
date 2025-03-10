<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Service;

use Doctrine\DBAL\Schema\Table;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Utility\MathUtility;

class TcaSchemaService
{
    public function getColumnInformationBasedOnTca(string $columnType, array $fieldConfig): array
    {
        $columnInformation = [];
        if ($columnType === '') {
            return $columnInformation;
        }

        switch ($columnType) {
            case 'category':
                if (($fieldConfig['relationship'] ?? '') === 'oneToMany') {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'TEXT',
                        [
                            'notnull' => false,
                        ]
                    );
                } else {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'INT',
                        [
                            'default' => 0,
                            'notnull' => true,
                            'unsigned' => true,
                        ]
                    );
                }
                break;

            case 'datetime':
                $dbType = $fieldConfig['dbType'] ?? '';
                // Add datetime fields for all tables, defining datetime columns (TCA type=datetime), except
                // those columns, which had already been added due to definition in "ctrl", e.g. "starttime".
                if (in_array($dbType, QueryHelper::getDateTimeTypes(), true)) {
                    $nullable = $fieldConfig['nullable'] ?? true;
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        $dbType,
                        [
                            // native datetime fields are nullable by default, and
                            // are only not-nullable if `nullable` is explicitly set to false.
                            'notnull' => !$nullable,
                        ]
                    );
                } else {
                    // int unsigned:            from 1970 to 2106.
                    // int signed:              from 1901 to 2038.
                    // bigint unsigned/signed:  from whenever to whenever
                    //
                    // Anything like crdate,tstamp,starttime,endtime is good with
                    //  "int unsigned" and can survive the 2038 apocalypse (until 2106).
                    //
                    // However, anything that has birthdates or dates
                    // from the past (sys_file_metadata.content_creation_date) was saved
                    // as a SIGNED INT. It allowed birthdays of people older than 1970,
                    // but with the downside that it ends in 2038.
                    //
                    // This is now changed to utilize BIGINT everywhere, even when smaller
                    // date ranges are requested. To reduce complexity, we specifically
                    // do not evaluate "range.upper/lower" fields and use a unified type here.
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'BIGINT',
                        [
                            'default' => 0,
                            'notnull' => !($fieldConfig['nullable'] ?? false),
                            'unsigned' => false,
                        ]
                    );
                }
                break;

            case 'slug':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'TEXT',
                    [
                        'length' => 65535,
                        'notnull' => false,
                    ]
                );
                break;

            case 'json':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'JSON',
                    [
                        'notnull' => false,
                    ]
                );
                break;

            case 'uuid':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'VARCHAR',
                    [
                        'length' => 36,
                        'default' => '',
                        'notnull' => true,
                    ]
                );
                break;

            case 'file':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'INT',
                    [
                        'default' => 0,
                        'notnull' => true,
                        'unsigned' => true,
                    ]
                );
                break;

            case 'folder':
            case 'imageManipulation':
            case 'flex':
            case 'text':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'TEXT',
                    [
                        'notnull' => false,
                    ]
                );
                break;

            case 'email':
                $isNullable = (bool)($fieldConfig['nullable'] ?? false);
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'VARCHAR',
                    [
                        'length' => 255,
                        'default' => ($isNullable ? null : ''),
                        'notnull' => !$isNullable,
                    ]
                );
                break;

            case 'check':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'SMALLINT',
                    [
                        'default' => $fieldConfig['default'] ?? 0,
                        'notnull' => true,
                        'unsigned' => true,
                    ]
                );
                break;

            case 'language':
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'INT',
                    [
                        'default' => 0,
                        'notnull' => true,
                        'unsigned' => false,
                    ]
                );
                break;

            case 'group':
                if (isset($fieldConfig['MM'])) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'INT',
                        [
                            'default' => 0,
                            'notnull' => true,
                            'unsigned' => true,
                        ]
                    );
                } else {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'TEXT',
                        [
                            'notnull' => false,
                        ]
                    );
                }
                break;

            case 'password':
                if ($fieldConfig['nullable'] ?? false) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'default' => null,
                            'notnull' => false,
                        ]
                    );
                } else {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'default' => '',
                            'notnull' => true,
                        ]
                    );
                }
                break;

            case 'color':
                $opacity = (bool)($fieldConfig['opacity'] ?? false);
                if ($fieldConfig['nullable'] ?? false) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'length' => $opacity ? 9 : 7,
                            'default' => null,
                            'notnull' => false,
                        ]
                    );
                } else {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'length' => $opacity ? 9 : 7,
                            'default' => '',
                            'notnull' => true,
                        ]
                    );
                }
                break;

            case 'radio':
                $hasItemsProcFunc = ($fieldConfig['itemsProcFunc'] ?? '') !== '';
                $items = $fieldConfig['items'] ?? [];
                // With itemsProcFunc we can't be sure, which values are persisted. Use type string.
                if ($hasItemsProcFunc) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'length' => 255,
                            'default' => '',
                            'notnull' => true,
                        ]
                    );
                    break;
                }
                // If no items are configured, use type string to be safe for values added directly.
                if ($items === []) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'length' => 255,
                            'default' => '',
                            'notnull' => true,
                        ]
                    );
                    break;
                }
                // If only one value is NOT an integer use type string.
                foreach ($items as $item) {
                    if (!MathUtility::canBeInterpretedAsInteger($item['value'])) {
                        $columnInformation = $this->getColumnInformation(
                            $this->quote($fieldConfig['columnName']),
                            'VARCHAR',
                            [
                                'length' => 255,
                                'default' => '',
                                'notnull' => true,
                            ]
                        );
                        // continue with next $tableDefinition['columns']
                        // see: DefaultTcaSchemaTest->enrichAddsRadioStringVerifyThatCorrectLoopIsContinued()
                        break 2;
                    }
                }
                // Use integer type.
                $allValues = array_map(fn(array $item): int => (int)$item['value'], $items);
                $minValue = min($allValues);
                $maxValue = max($allValues);
                // Try to safe some bytes - can be reconsidered to simply use 'INT'.
                $integerType = ($minValue >= -32768 && $maxValue < 32768)
                    ? 'SMALLINT'
                    : 'INT';
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    $integerType,
                    [
                        'default' => 0,
                        'notnull' => true,
                    ]
                );
                break;

            case 'link':
                $nullable = $fieldConfig['nullable'] ?? false;
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'TEXT',
                    [
                        'length' => 65535,
                        'default' => $nullable ? null : '',
                        'notnull' => !$nullable,
                    ]
                );
                break;

            case 'input':
                $length = (int)($fieldConfig['max'] ?? 255);
                $nullable = $fieldConfig['nullable'] ?? false;
                if ($length > 255) {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'TEXT',
                        [
                            'length' => 65535,
                            'default' => $nullable ? null : '',
                            'notnull' => !$nullable,
                        ]
                    );
                    break;
                }
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'VARCHAR',
                    [
                        'length' => $length,
                        'default' => '',
                        'notnull' => !$nullable,
                    ]
                );
                break;

            case 'inline':
                if (($fieldConfig['MM'] ?? '') !== '' || ($fieldConfig['foreign_field'] ?? '') !== '') {
                    // Parent "count" field
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'INT',
                        [
                            'default' => 0,
                            'notnull' => true,
                            'unsigned' => true,
                        ]
                    );
                } else {
                    // Inline "csv"
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'default' => '',
                            'notnull' => true,
                            'length' => 255,
                        ]
                    );
                }
                break;

            case 'number':
                $type = ($fieldConfig['format'] ?? '') === 'decimal' ? 'DECIMAL' : 'INT';
                $nullable = $fieldConfig['nullable'] ?? false;
                $lowerRange = $fieldConfig['range']['lower'] ?? -1;
                // Integer type for all database platforms.
                if ($type === 'INT') {
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'INT',
                        [
                            'default' => $nullable === true ? null : 0,
                            'notnull' => !$nullable,
                            'unsigned' => $lowerRange >= 0,
                        ]
                    );
                    break;
                }

                // Decimal for all supported platforms except SQLite
                $columnInformation = $this->getColumnInformation(
                    $this->quote($fieldConfig['columnName']),
                    'DECIMAL',
                    [
                        'default' => $nullable === true ? null : 0.00,
                        'notnull' => !$nullable,
                        'unsigned' => $lowerRange >= 0,
                        'precision' => 10,
                        'scale' => 2,
                    ]
                );
                break;

            case 'select':
                if (($fieldConfig['MM'] ?? '') !== '') {
                    // MM relation, this is a "parent count" field. Have an int.
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'INT',
                        [
                            'notnull' => true,
                            'default' => 0,
                            'unsigned' => true,
                        ]
                    );
                    break;
                }
                $dbFieldLength = (int)($fieldConfig['dbFieldLength'] ?? 0);
                // If itemsProcFunc is not set, check the item values
                if (($fieldConfig['itemsProcFunc'] ?? '') === '') {
                    $items = $fieldConfig['items'] ?? [];
                    $itemsContainsOnlyIntegers = true;
                    foreach ($items as $item) {
                        if (!MathUtility::canBeInterpretedAsInteger($item['value'])) {
                            $itemsContainsOnlyIntegers = false;
                            break;
                        }
                    }
                    $itemsAreAllPositive = true;
                    foreach ($items as $item) {
                        if ($item['value'] < 0) {
                            $itemsAreAllPositive = false;
                            break;
                        }
                    }
                    // @todo: The dependency to renderType is unfortunate here. It's only purpose is to potentially have int fields
                    //        instead of string when this is a 'single' relation / value. However, renderType should usually not
                    //        influence DB layer at all. Maybe 'selectSingle' should be changed to an own 'type' instead to make
                    //        this more explicit. Maybe DataHandler could benefit from this as well?
                    if (($fieldConfig['renderType'] ?? '') === 'selectSingle' || ($fieldConfig['maxitems'] ?? 0) === 1) {
                        // With 'selectSingle' or with 'maxitems = 1', only a single value can be selected.
                        if (
                            !is_array($fieldConfig['fileFolderConfig'] ?? false)
                            && ($items !== [] || ($fieldConfig['foreign_table'] ?? '') !== '')
                            && $itemsContainsOnlyIntegers === true
                        ) {
                            // If the item list is empty, or if it contains only int values, an int field is enough.
                            // Also, the config must not be a 'fileFolderConfig' field which takes string values.
                            $columnInformation = $this->getColumnInformation(
                                $this->quote($fieldConfig['columnName']),
                                'INT',
                                [
                                    'notnull' => true,
                                    'default' => 0,
                                    'unsigned' => $itemsAreAllPositive,
                                ]
                            );
                            break;
                        }
                        // If int is no option, have a string field.
                        $columnInformation = $this->getColumnInformation(
                            $this->quote($fieldConfig['columnName']),
                            'VARCHAR',
                            [
                                'notnull' => true,
                                'default' => '',
                                'length' => $dbFieldLength > 0 ? $dbFieldLength : 255,
                            ]
                        );
                        break;
                    }
                    if ($itemsContainsOnlyIntegers) {
                        // Multiple values can be selected and will be stored comma separated. When manual item values are
                        // all integers, or if there is a foreign_table, we end up with a comma separated list of integers.
                        // Using string / varchar 255 here should be long enough to store plenty of values, and can be
                        // changed by setting 'dbFieldLength'.
                        $columnInformation = $this->getColumnInformation(
                            $this->quote($fieldConfig['columnName']),
                            'VARCHAR',
                            [
                                // @todo: nullable = true is not a good default here. This stems from the fact that this
                                //        if triggers a lot of TEXT->VARCHAR() field changes during upgrade, where TEXT
                                //        is always nullable, but varchar() is not. As such, we for now declare this
                                //        nullable, but could have a look at it later again when a value upgrade
                                //        for such cases is in place that updates existing null fields to empty string.
                                'notnull' => false,
                                'default' => '',
                                'length' => $dbFieldLength > 0 ? $dbFieldLength : 255,
                            ]
                        );
                        break;
                    }
                }
                if ($dbFieldLength > 0) {
                    // If nothing else matches, but there is a dbFieldLength set, have varchar with that length.
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'VARCHAR',
                        [
                            'notnull' => true,
                            'default' => '',
                            'length' => $dbFieldLength,
                        ]
                    );
                } else {
                    // Final fallback creates a (nullable) text field.
                    $columnInformation = $this->getColumnInformation(
                        $this->quote($fieldConfig['columnName']),
                        'TEXT',
                        [
                            'notnull' => false,
                        ]
                    );
                }
                break;
        }

        return $columnInformation;
    }

    private function getColumnInformation(string $quotedColumnName, string $columnType, array $options): array
    {
        $options['columnName'] = $quotedColumnName;
        $options['columnType'] = $columnType;

        return $options;
    }

    /**
     * True if an index with a given name is defined within the incoming
     * array of Table's.
     *
     * @param array<non-empty-string, Table> $tables
     */
    protected function isIndexDefinedForTable(array $tables, string $tableName, string $indexName): bool
    {
        return ($tables[$tableName] ?? null)?->hasIndex($indexName) ?? false;
    }

    protected function quote(string $identifier): string
    {
        return '`' . $identifier . '`';
    }
}
