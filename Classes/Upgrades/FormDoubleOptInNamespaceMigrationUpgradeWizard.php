<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\ConfirmableInterface;
use TYPO3\CMS\Install\Updates\Confirmation;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('formDoubleOptInNamespaceMigrationMigration')]
class FormDoubleOptInNamespaceMigrationUpgradeWizard implements UpgradeWizardInterface, ConfirmableInterface
{
    protected Confirmation $confirmation;

    public function __construct()
    {
        $this->confirmation = new Confirmation(
            'Please make sure to read the following carefully:',
            $this->getDescription(),
            false,
            'Yes, I understand!',
            'No thanks',
            false,
        );
    }

    public function getIdentifier(): string
    {
        return 'formDoubleOptInNamespaceMigrationMigration';
    }

    public function getTitle(): string
    {
        return 'EXT:form_double_opt_in: Migrate Form Finishers to the new Namespace';
    }

    /**
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Migrate usage of `Medienreaktor\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher` in form ' .
            'finishers to `LinaWolf\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher` manually.';
    }

    public function updateNecessary(): bool
    {
        return true;
    }

    /**
     * @return string[] All new fields and tables must exist
     */
    public function getPrerequisites(): array
    {
        return [
        ];
    }

    /**
     * This upgrade wizard has informational character only, it does not perform actions.
     */
    public function executeUpdate(): bool
    {
        return true;
    }

    /**
     * Return a confirmation message instance
     */
    public function getConfirmation(): Confirmation
    {
        return $this->confirmation;
    }
}
