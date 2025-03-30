<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Extension Kickstarter',
    'description' => 'Kickstart TYPO3 Extension',
    'category' => 'module',
    'author' => 'Stefan Froemken',
    'author_email' => 'froemken@gmail.com',
    'state' => 'beta',
    'author_company' => '',
    'version' => '0.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.3-13.4.99',
        ],
        'conflicts' => [
            'make' => '*',
        ],
        'suggests' => [
        ],
    ],
];
