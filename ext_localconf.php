<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use LinaWolf\FormDoubleOptIn\Controller\DoubleOptInController;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Scheduler\Task\TableGarbageCollectionTask;
use LinaWolf\FormDoubleOptIn\Updates\FormDoubleOptInNamespaceMigration;

defined('TYPO3') || die('Access denied.');

call_user_func(
    function ($extKey) {
        ExtensionUtility::configurePlugin(
            'form_double_opt_in',
            'DoubleOptIn',
            [
                DoubleOptInController::class => 'validation,delete',
            ],
            [
                DoubleOptInController::class => 'validation,delete',
            ]
        );

        $iconRegistry = GeneralUtility::makeInstance(
            IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'plugin-doubleoptin',
            SvgIconProvider::class,
            ['source' => 'EXT:form_double_opt_in/Resources/Public/Icons/PluginDoubleOptIn.svg']
        );

        ExtensionManagementUtility::addPageTSConfig(
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

        if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][TableGarbageCollectionTask::class]['options']['tables'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][TableGarbageCollectionTask::class]['options']['tables']['tx_formdoubleoptin_domain_model_optin'] = [
                'dateField' => 'tstamp',
                'expirePeriod' => 30,
            ];
        }

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['FormDoubleOptInNamespaceMigrationMigration']
            = FormDoubleOptInNamespaceMigration::class;
    },
    'form_double_opt_in'
);
