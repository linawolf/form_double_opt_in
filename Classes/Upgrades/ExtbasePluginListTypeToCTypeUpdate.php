<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Upgrades;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate;

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
