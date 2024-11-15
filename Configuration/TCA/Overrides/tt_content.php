<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'form_double_opt_in',
    'DoubleOptIn',
    'DoubleOptIn',
    'plugin-doubleoptin',
    'forms',
    'Validation and enabling of Double Opt-In entries.',
);
