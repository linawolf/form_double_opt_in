<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'form_double_opt_in',
    'Configuration/TypoScript',
    'FormDoubleOptIn (Deprecated, use Site Sets)',
);
