<?php

use StefanFroemken\ExtKickstarter\Controller;

return [
    'ext_kickstarter_build' => [
        'path' => '/ext-kickstarter/build',
        'methods' => ['POST'],
        'target' => Controller\KickstartController::class . '::build',
    ],
];
