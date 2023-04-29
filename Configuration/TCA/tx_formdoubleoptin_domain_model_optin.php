<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'registration_date DESC, crdate DESC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'email, title, given_name, family_name, company, customer_number, validation_hash',
        'iconfile' => 'EXT:form_double_opt_in/Resources/Public/Icons/PluginDoubleOptIn.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'pagelanguage, email, registration_date, mail_body, title, given_name, family_name, company, customer_number, validation_hash, validation_date, is_validated',
        ],
    ],
    'columns' => [
        'pagelanguage' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.pagelanguage',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'readOnly' => 1,
            ],
        ],
        'email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.email',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'mail_body' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.mail_body',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
            ],
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'given_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.given_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'family_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.family_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'company' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.company',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'customer_number' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.customer_number',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'readOnly' => 1,
            ],
        ],
        'is_validated' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.is_validated',
            'config' => [
                'type' => 'check',
                'readOnly' => 1,
            ],
        ],
        'validation_hash' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.validation_hash',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'readOnly' => 1,
            ],
        ],
        'registration_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.registration_date',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'datetime',
                'renderType' => 'inputDateTime',
                'checkbox' => 0,
                'readOnly' => 1,
            ],
        ],
        'validation_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:form_double_opt_in/Resources/Private/Language/locallang.xlf:tx_formdoubleoptin_domain_model_optin.validation_date',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'datetime',
                'renderType' => 'inputDateTime',
                'checkbox' => 0,
                'readOnly' => 1,
            ],
        ],
    ],
];
