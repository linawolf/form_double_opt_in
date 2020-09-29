<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'email, title, given_name, family_name, company, customer_number, validation_hash',
        'iconfile' => 'EXT:form_double_opt_in/Resources/Public/Icons/PluginDoubleOptIn.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'email, title, given_name, family_name, company, customer_number, validation_hash, validation_date, is_validated'
    ],
    'types' => [
        '1' => [
            'showitem' => 'email, title, given_name, family_name, company, customer_number, validation_hash, validation_date, is_validated'
        ]
    ],
    'columns' => [
        'email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'given_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.given_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'family_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.family_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'company' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.company',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'customer_number' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.customer_number',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1
            ]
        ],
        'is_validated' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.is_validated',
            'config' => [
                'type' => 'check',
                'readOnly' => 1
            ]
        ],
        'validation_hash' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.validation_hash',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'readOnly' => 1
            ]
        ],
        'validation_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.validation_date',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'readOnly' => 1
            ]
        ],
    ]
];
