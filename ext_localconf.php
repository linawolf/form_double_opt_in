<?php

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($extKey) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'form_double_opt_in',
            'DoubleOptIn',
            [
                \Medienreaktor\FormDoubleOptIn\Controller\DoubleOptInController::class => 'validation,delete',
            ],
            [
                \Medienreaktor\FormDoubleOptIn\Controller\DoubleOptInController::class => 'validation,delete',
            ]
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'plugin-doubleoptin',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:form_double_opt_in/Resources/Public/Icons/PluginDoubleOptIn.svg']
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
    			wizards.newContentElement.wizardItems.plugins {
    				elements {
    					formdoubleoptin_doubleoptin {
                            iconIdentifier = plugin-doubleoptin
    						title = Double Opt-In Validation
    						description = Validation and enabling of Double Opt-In entries.
    						tt_content_defValues {
    							CType = list
    							list_type = formdoubleoptin_doubleoptin
    						}
    					}
    				}
    				show = *
    			}
    	   }'
        );

        if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask::class]['options']['tables'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask::class]['options']['tables']['tx_formdoubleoptin_domain_model_optin'] = [
                'dateField' => 'tstamp',
                'expirePeriod' => 30,
            ];
        }
    },
    'form_double_opt_in'
);
