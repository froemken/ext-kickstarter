<?php

use FriendsOfTYPO3\Kickstarter\Controller;

return [
    'ext_kickstarter_build' => [
        'path' => '/ext-kickstarter/build',
        'methods' => ['POST'],
        'target' => Controller\KickstartController::class . '::build',
    ],
];
