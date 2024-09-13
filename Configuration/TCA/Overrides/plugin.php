<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(
    function ($extKey) {
        ExtensionUtility::registerPlugin(
            'form_double_opt_in',
            'DoubleOptIn',
            'DoubleOptIn',
        );

        ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', 'FormDoubleOptIn');
        ExtensionManagementUtility::allowTableOnStandardPages('tx_formdoubleoptin_domain_model_optin');
    },
    'form_double_opt_in',
);
