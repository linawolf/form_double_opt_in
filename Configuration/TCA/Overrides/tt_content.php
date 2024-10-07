<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(static function (): void {
    ExtensionUtility::registerPlugin(
        'form_double_opt_in',
        'DoubleOptIn',
        'DoubleOptIn',
    );
});
