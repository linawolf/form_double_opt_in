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

    /**
     * @return string
     */
    public function getPagelanguage(): string
    {
        return $this->pagelanguage;
    }

    /**
     * @param string $pagelanguage
     */
    public function setPagelanguage(string $pageLanguage): void
    {
        $this->pagelanguage = $pageLanguage;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    /**
     * @param string $familyName
     */
    public function setFamilyName(string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }

    /**
     * @param string $customerNumber
     */
    public function setCustomerNumber(string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    /**
     * @param bool $isValidated
     */
    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }

    /**
     * @return string
     */
    public function getValidationHash(): string
    {
        return $this->validationHash;
    }

    /**
     * @param string $validationHash
     */
    public function setValidationHash(string $validationHash): void
    {
        $this->validationHash = $validationHash;
    }

    /**
     * @return \DateTime|null
     */
    public function getValidationDate(): ?\DateTime
    {
        return $this->validationDate;
    }

    /**
     * @param \DateTime|null $validationDate
     */
    public function setValidationDate(?\DateTime $validationDate): void
    {
        $this->validationDate = $validationDate;
    }

    /**
     * @return \DateTime|null
     */
    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTime|null $registrationDate
     */
    public function setRegistrationDate(?\DateTime $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return string
     */
    public function getMailBody(): string
    {
        return $this->mailBody;
    }

    /**
     * @param string $mailBody
     */
    public function setMailBody(string $mailBody): void
    {
        $this->mailBody = $mailBody;
    }
}
