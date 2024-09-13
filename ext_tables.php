<?php

use TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask;

defined('TYPO3') || die('Access denied.');

call_user_func(
    function ($extKey) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']
        [TableGarbageCollectionTask::class]['options']['tables']
        ['tx_formdoubleoptin_domain_model_optin'] = [
            'dateField' => 'tstamp',
            'expirePeriod' => 14,
        ];
    },
    'form_double_opt_in',
);
