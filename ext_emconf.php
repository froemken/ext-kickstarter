<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Kickstarter',
    'description' => 'Kickstart TYPO3 Extension',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'beta',
    'author_company' => '',
    'version' => '0.1.4',
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
