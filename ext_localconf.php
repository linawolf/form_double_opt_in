<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Medienreaktor.FormDoubleoptin',
            'DoubleOptIn',
            [
                'DoubleOptIn' => 'validation'
            ],
            [
                'DoubleOptIn' => 'validation'
            ]
        );

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
           \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'plugin-doubleoptin',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:form_doubleoptin/Resources/Public/Icons/PluginDoubleOptIn.svg']
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
    },
    $_EXTKEY
);
