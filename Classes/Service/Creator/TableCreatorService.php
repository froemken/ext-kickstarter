<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Service\Creator;

use FriendsOfTYPO3\Kickstarter\Information\TableInformation;

readonly class TableCreatorService
{
    public const TABLE_COLUMN_TYPES = [
        'category' => [
            'type' => 'category',
        ],
        'check' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => 'Change me',
                ],
            ],
        ],
        'color' => [
            'type' => 'color',
        ],
        'datetime' => [
            'type' => 'datetime',
            'format' => 'date',
            'default' => 0,
        ],
        'email' => [
            'type' => 'email',
        ],
        'file' => [
            'type' => 'file',
            'maxitems' => 1,
            'allowed' => 'common-image-types',
        ],
        'flex' => [
            'type' => 'flex',
        ],
        'folder' => [
            'type' => 'folder',
        ],
        'group' => [
            'type' => 'group',
            'allowed' => '',
        ],
        'imageManipulation' => [
            'type' => 'imageManipulation',
        ],
        'inline' => [
            'type' => 'inline',
        ],
        'input' => [
            'type' => 'input',
        ],
        'json' => [
            'type' => 'json',
        ],
        'language' => [
            'type' => 'language',
        ],
        'link' => [
            'type' => 'link',
        ],
        'none' => [
            'type' => 'none',
        ],
        'number' => [
            'type' => 'number',
        ],
        'passthrough' => [
            'type' => 'passthrough',
        ],
        'password' => [
            'type' => 'password',
        ],
        'radio' => [
            'type' => 'radio',
            'items' => [
                [
                    'label' => 'Change me',
                    'value' => 1,
                ],
            ],
        ],
        'select' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    'label' => 'Change me',
                    'value' => 1,
                ],
            ],
        ],
        'slug' => [
            'type' => 'slug',
        ],
        'text' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 7,
        ],
        'user' => [
            'type' => 'user',
        ],
        'uuid' => [
            'type' => 'uuid',
        ],
    ];

    public function __construct(
        private iterable $tableCreators,
    ) {}

    public function create(TableInformation $tableInformation): void
    {
        foreach ($this->tableCreators as $creator) {
            $creator->create($tableInformation);
        }
    }
}
