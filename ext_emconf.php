<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Kickstarter',
    'description' => 'Kickstart TYPO3 Extension',
    'category' => 'module',
    'author' => 'Kickstarter Development Team',
    'author_email' => 'friendsof@typo3.org',
    'state' => 'beta',
    'author_company' => '',
    'version' => '0.1.5',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'install' => '12.4.0-12.4.99',
        ],
        'conflicts' => [
            'make' => '*',
        ],
        'suggests' => [
        ],
    ],
];
