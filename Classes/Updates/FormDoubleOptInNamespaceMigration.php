<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Updates;

use TYPO3\CMS\Install\Updates\ConfirmableInterface;
use TYPO3\CMS\Install\Updates\Confirmation;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class FormDoubleOptInNamespaceMigration implements UpgradeWizardInterface, ConfirmableInterface
{
    /**
     * @var Confirmation
     */
    protected $confirmation;

    public function __construct()
    {
        $this->confirmation = new Confirmation(
            'Please make sure to read the following carefully:',
            $this->getDescription(),
            false,
            'Yes, I understand!',
            'No thanks',
            false
        );
    }

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return 'FormDoubleOptInNamespaceMigrationMigration';
    }

    /**
     * @return string Title of this updater
     */
    public function getTitle(): string
    {
        return 'form_double_opt_in: Migrate Form Finishers to the new Namespace';
    }

    /**
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Migrate usage of `Medienreaktor\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher` in form ' .
            'finishers to `Medienreaktor\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher` manually.';
    }

    /**
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
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
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    /**
     * This upgrade wizard has informational character only, it does not perform actions.
     *
     * @return bool Whether everything went smoothly or not
     */
    public function executeUpdate(): bool
    {
        return true;
    }

    /**
     * Return a confirmation message instance
     *
     * @return Confirmation
     */
    public function getConfirmation(): Confirmation
    {
        return $this->confirmation;
    }
}
