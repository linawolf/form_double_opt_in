<?php

declare(strict_types=1);

use LinaWolf\FormDoubleOptIn\Controller\DoubleOptInController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask;

defined('TYPO3') or die();

call_user_func(static function (): void {
    ExtensionUtility::configurePlugin(
        'form_double_opt_in',
        'DoubleOptIn',
        [
            DoubleOptInController::class => 'validation,delete',
        ],
        [
            DoubleOptInController::class => 'validation,delete',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );

    ExtensionManagementUtility::addTypoScript(
        'form_double_opt_in',
        'setup',
        "@import 'EXT:form_double_opt_in/Configuration/TypoScript/backend.typoscript'",
    );

    // Configure the table garbage collection task.
    if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][
        TableGarbageCollectionTask::class]['options']['tables'])
    ) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']
        [TableGarbageCollectionTask::class]['options']['tables']
        ['tx_formdoubleoptin_domain_model_optin'] = [
            'dateField' => 'tstamp',
            'expirePeriod' => 30,
        ];
    }
});
