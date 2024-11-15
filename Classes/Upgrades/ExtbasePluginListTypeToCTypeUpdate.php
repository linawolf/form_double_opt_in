<?php

namespace LinaWolf\FormDoubleOptIn\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('formDoubleOptInPluginListTypeToCTypeUpdate')]
final class ExtbasePluginListTypeToCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'formdoubleoptin_doubleoptin' => 'formdoubleoptin_doubleoptin',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrates form_double_opt_in plugins';
    }

    public function getDescription(): string
    {
        return 'Migrates formdoubleoptin_doubleoptin  from list_type to CType. ';
    }
}
