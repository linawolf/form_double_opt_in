<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Domain\Model;

use LinaWolf\FormDoubleOptIn\Utility\Uuid;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * OptIn
 */
class OptIn extends AbstractEntity
{
    protected string $pagelanguage = '';
    protected string $email = '';
    protected string $title = '';
    protected string $givenName = '';
    protected string $familyName = '';
    protected string $company = '';
    protected string $customerNumber = '';
    protected bool $isValidated = false;
    protected string $validationHash = '';
    protected ?\DateTime $validationDate = null;
    protected ?\DateTime $registrationDate = null;
    protected string $mailBody = '';

    public function __construct()
    {
        if (!$this->validationHash) {
            $this->validationHash = Uuid::generate();
        }
    }

    public function getPagelanguage(): string
    {
        return $this->pagelanguage;
    }

    public function setPagelanguage(string $pageLanguage): void
    {
        $this->pagelanguage = $pageLanguage;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }

    public function setCustomerNumber(string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }

    public function getValidationHash(): string
    {
        return $this->validationHash;
    }

    public function setValidationHash(string $validationHash): void
    {
        $this->validationHash = $validationHash;
    }

    public function getValidationDate(): ?\DateTime
    {
        return $this->validationDate;
    }

    public function setValidationDate(?\DateTime $validationDate): void
    {
        $this->validationDate = $validationDate;
    }

    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTime $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function getMailBody(): string
    {
        return $this->mailBody;
    }

    public function setMailBody(string $mailBody): void
    {
        $this->mailBody = $mailBody;
    }
}
