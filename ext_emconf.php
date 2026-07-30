<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Form Double Opt-In',
    'description' => 'Double Opt-In for the TYPO3 CMS Form Framework',
    'category' => 'plugin',
    'state' => 'stable',
    'version' => '13.2.3',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.3.99',
            'form' => '14.3.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
