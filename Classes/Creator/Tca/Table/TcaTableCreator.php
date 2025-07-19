<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Tca\Table;

use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use StefanFroemken\ExtKickstarter\Information\TableInformation;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\FileStructure;
use StefanFroemken\ExtKickstarter\PhpParser\Structure\ReturnStructure;
use StefanFroemken\ExtKickstarter\Traits\FileStructureBuilderTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaTableCreator implements TcaTableCreatorInterface
{
    use FileStructureBuilderTrait;

    private BuilderFactory $factory;

    public function __construct()
    {
        $this->factory = new BuilderFactory();
    }

    public function create(TableInformation $tableInformation): void
    {
        GeneralUtility::mkdir_deep($tableInformation->getTableConfigurationPath());

        $tableFile = $tableInformation->getFullTableConfigurationFilePath();
        $fileStructure = $this->buildFileStructure($tableFile);

        if (!is_file($tableFile)) {
            $this->addTableNode($fileStructure, $tableInformation);
        }

        $columnsItem = $this->getExistingTcaSection($fileStructure, 'columns');
        if ($columnsItem instanceof ArrayItem && $columnsItem->value instanceof Array_) {
            $columnsItem->value->items = $this->addNewTcaColumns($columnsItem->value->items, $tableInformation);
        }

        $typesItem = $this->getExistingTcaSection($fileStructure, 'types');
        if ($typesItem instanceof ArrayItem && $typesItem->value instanceof Array_) {
            $this->addNewTcaTypeItems($typesItem->value->items, $tableInformation);
        }

        file_put_contents($tableFile, $fileStructure->getFileContents());
    }

    private function getExistingTcaSection(FileStructure $fileStructure, string $section): ?ArrayItem
    {
        if ($fileStructure->getReturnStructures()->count() === 0) {
            return null;
        }

        /** @var ReturnStructure $returnStructure */
        $returnStructure = $fileStructure->getReturnStructures()->current();

        if (($expr = $returnStructure->getNode()->expr) && $expr instanceof Array_) {
            foreach ($expr->items as $item) {
                if ($item->key instanceof String_ && $item->key->value === $section) {
                    return $item;
                }
            }
        }


        return null;
    }

    private function addNewTcaColumns(array $existingTcaColumns, TableInformation $tableInformation): array
    {
        $existingColumnNames = [];
        foreach ($existingTcaColumns as $existingTcaColumn) {
            $existingColumnNames[] = $existingTcaColumn->key->value;
        }

        foreach ($tableInformation->getColumns() as $columnName => $columnConfiguration) {
            if (in_array($columnName, $existingColumnNames, true)) {
                continue;
            }

            $existingTcaColumns[] = new ArrayItem($this->factory->val([
                'exclude' => true,
                'label' => $columnConfiguration['label'],
                'config' => $columnConfiguration['config'],
            ]), new String_($columnName));
        }

        return $existingTcaColumns;
    }

    private function addNewTcaTypeItems(array $existingTcaTypes, TableInformation $tableInformation): void
    {
        if (!isset($existingTcaTypes[0]->value->items[0]->value)) {
            return;
        }

        /** @var String_ $showItems */
        $showItems = $existingTcaTypes[0]->value->items[0]->value;
        $existingColumnNames = GeneralUtility::trimExplode(',', $showItems->value, true);

        foreach ($tableInformation->getColumns() as $columnName => $columnConfiguration) {
            if (in_array($columnName, $existingColumnNames, true)) {
                continue;
            }

            $existingColumnNames[] = $columnName;
        }

        $showItems->value = implode(', ', $existingColumnNames);
    }

    private function addTableNode(FileStructure $fileStructure, TableInformation $tableInformation): void
    {
        $fileStructure->addReturnStructure(
            new ReturnStructure(
                new Return_(
                    new Array_([
                        new ArrayItem($this->getCtrlArrayItems($tableInformation), new String_('ctrl')),
                        new ArrayItem($this->getTypesArrayItems($tableInformation), new String_('types')),
                        new ArrayItem($this->getPalettesArrayItems($tableInformation), new String_('palettes')),
                        new ArrayItem($this->getColumnsArrayItems($tableInformation), new String_('columns')),
                    ])
                )
            )
        );
    }

    private function getCtrlArrayItems(TableInformation $tableInformation): Expr
    {
        return $this->factory->val([
            'title' => $tableInformation->getTitle(),
            'label' => $tableInformation->getLabel(),
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'versioningWS' => true,
            'origUid' => 't3_origuid',
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
        ]);
    }

    private function getTypesArrayItems(TableInformation $tableInformation): Expr
    {
        return $this->factory->val([
            '0' => [
                'showitem' => 'hidden, sys_language_uid, l10n_diffsource',
            ],
        ]);
    }

    private function getPalettesArrayItems(TableInformation $tableInformation): Expr
    {
        return $this->factory->val([
            'access' => [
                'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            ],
        ]);
    }

    private function getColumnsArrayItems(TableInformation $tableInformation): Expr
    {
        return $this->factory->val([
            'sys_language_uid' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                'config' => [
                    'type' => 'language',
                ],
            ],
            'l10n_parent' => [
                'displayCond' => 'FIELD:sys_language_uid:>:0',
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'group',
                    'allowed' => $tableInformation->getTableName(),
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 0,
                    'default' => 0,
                    'suggestOptions' => [
                        'default' => [
                            'searchWholePhrase' => true,
                            'addWhere' => 'AND ' . $tableInformation->getTableName() . '.sys_language_uid IN (0,-1)',
                        ],
                    ],
                ],
            ],
            'l10n_diffsource' => [
                'config' => [
                    'type' => 'passthrough',
                    'default' => '',
                ],
            ],
            'hidden' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => [
                        [
                            'label' => '',
                            'value' => '',
                            'invertStateDisplay' => true,
                        ],
                    ],
                ],
            ],
            'starttime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                'config' => [
                    'type' => 'datetime',
                    'format' => 'datetime',
                    'default' => 0,
                ],
                'l10n_mode' => 'exclude',
                'l10n_display' => 'defaultAsReadonly',
            ],
            'endtime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                'config' => [
                    'type' => 'datetime',
                    'format' => 'datetime',
                    'default' => 0,
                    'range' => [
                        'upper' => mktime(0, 0, 0, 1, 1, 2038),
                    ],
                ],
                'l10n_mode' => 'exclude',
                'l10n_display' => 'defaultAsReadonly',
            ],
        ]);
    }
}
