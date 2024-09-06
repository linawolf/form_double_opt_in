<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Double Opt-In',
    'description' => 'Double Opt-In for the TYPO3 CMS Form Framework',
    'category' => 'plugin',
    'state' => 'stable',
    'version' => '12.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'form' => '12.4.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
