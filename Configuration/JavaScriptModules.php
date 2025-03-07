<?php

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'dependencies' => [
        'backend',
        'core',
    ],
    'imports' => [
        '@stefanfroemken/ext-kickstarter/' => [
            'path' => 'EXT:ext_kickstarter/Resources/Public/JavaScript/',
            // Exclude files of the following folders from being import-mapped
            'exclude' => [
                'EXT:ext_kickstarter/Resources/Public/JavaScript/Contrib/',
            ],
        ],
    ],
];
