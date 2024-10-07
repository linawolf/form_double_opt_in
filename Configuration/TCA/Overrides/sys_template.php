<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(static function (): void {
    ExtensionManagementUtility::addStaticFile(
        'form_double_opt_in',
        'Configuration/TypoScript',
        'FormDoubleOptIn',
    );

    ExtensionManagementUtility::allowTableOnStandardPages('tx_formdoubleoptin_domain_model_optin');
});
