<?php

use StefanFroemken\ExtKickstarter\Controller\KickstartController;

return [
    'ext_kickstarter_build' => [
        'path' => '/ext-kickstarter/build',
        'methods' => ['POST'],
        'target' => KickstartController::class . '::build',
    ],
];
