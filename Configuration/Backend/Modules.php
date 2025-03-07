<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Definitions for modules provided by EXT:ext_kickstarter
 */
return [
    'system_kickstarter' => [
        'parent' => 'system',
        'position' => ['after' => '*'],
        'access' => 'admin',
        'path' => '/module/ext_kickstarter/overview',
        'icon' => 'EXT:ext_kickstarter/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:ext_kickstarter/Resources/Private/Language/locallang_kickstarter.xlf',
        'routes' => [
            '_default' => [
                'target' => \StefanFroemken\ExtKickstarter\Controller\KickstartController::class . '::processRequest',
            ],
        ],
    ],
];
