<?php

call_user_func(
    function($extKey)
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'form_double_opt_in',
            'DoubleOptIn',
            'DoubleOptIn'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($extKey, 'Configuration/TypoScript', 'FormDoubleOptIn');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_formdoubleoptin_domain_model_optin');
    },
    'form_double_opt_in'
);
