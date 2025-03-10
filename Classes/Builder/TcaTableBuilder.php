<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Builder;

use StefanFroemken\ExtKickstarter\Builder\TcaType\CheckTcaTypeBuilder;
use StefanFroemken\ExtKickstarter\Builder\TcaType\DefaultTcaTypeBuilder;
use StefanFroemken\ExtKickstarter\Model\Graph;
use StefanFroemken\ExtKickstarter\Model\Node\Tca\TableNode;
use StefanFroemken\ExtKickstarter\Traits\WrapTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get file content for TCA files in Configuration/TCA
 */
class TcaTableBuilder implements BuilderInterface
{
    use WrapTrait;

    public function build(Graph $graph, string $extPath): void
    {
        $tableNodes = $graph->getExtensionNode()->getTableNodes();
        if ($tableNodes->count() === 0) {
            return;
        }

        foreach ($tableNodes as $tableNode) {
            if ($tableNode->getColumnNodes()->count() === 0) {
                continue;
            }

            // Must be set, else SQL error
            if ($tableNode->getLabel() === '') {
                continue;
            }

            $tablePath = $extPath . '/Configuration/TCA/';
            GeneralUtility::mkdir_deep($tablePath);

            file_put_contents(
                $tablePath . $tableNode->getTableFilename(),
                $this->getFileContent($tableNode)
            );
        }
    }

    private function getFileContent(TableNode $tableNode): string
    {
        return str_replace(
            [
                '{{TABLE_TITLE}}',
                '{{TABLE_LABEL}}',
                '{{TABLE_NAME}}',
                '{{SHOW_COLUMNS}}',
                '{{COLUMNS}}',
            ],
            [
                $tableNode->getTitle(),
                $tableNode->getLabel(),
                $tableNode->getTableName(),
                $this->getShowColumns($tableNode),
                implode(chr(10), $this->wrap($this->getColumnLines($tableNode), [], [], 2)),
            ],
            $this->getTemplate()
        );
    }

    private function getShowColumns(TableNode $tableNode): string
    {
        $columnNames = [];
        foreach ($tableNode->getColumnNodes() as $columnNode) {
            $columnNames[] = $columnNode->getColumnName();
        }

        return implode(',', $columnNames);
    }

    private function getColumnLines(TableNode $tableNode): array
    {
        $configLines = [];
        foreach ($tableNode->getColumnNodes() as $columnNode) {
            if ($columnNode->getColumnType() === 'check') {
                $tcaTypeBuilder = new CheckTcaTypeBuilder();
            } else {
                $tcaTypeBuilder = new DefaultTcaTypeBuilder();
            }
            array_push(
                $configLines,
                ...explode(chr(10), $tcaTypeBuilder->getFileContent($columnNode))
            );
        }

        return $configLines;
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<?php

return [
    'ctrl' => [
        'title' => '{{TABLE_TITLE}}',
        'label' => '{{TABLE_LABEL}}',
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
    ],
    'types' => [
        '0' => [
            'showitem' => '{{SHOW_COLUMNS}},
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access',
        ],
    ],
    'palettes' => [
        'access' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
        ],
    ],
    'columns' => [
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
                'allowed' => '{{TABLE_NAME}}',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
                'suggestOptions' => [
                    'default' => [
                        'searchWholePhrase' => true,
                        'addWhere' => 'AND {{TABLE_NAME}}.sys_language_uid IN (0,-1)',
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
{{COLUMNS}}
    ],
];
EOT;
    }
}
