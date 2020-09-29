<?php
namespace Medienreaktor\FormDoubleOptIn\Domain\Model;

/**
 * OptIn
 */
class OptIn extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * pagelanguage
     *
     * @var string
     */
    protected $pagelanguage = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * givenName
     *
     * @var string
     */
    protected $givenName = '';

    /**
     * familyName
     *
     * @var string
     */
    protected $familyName = '';

    /**
     * company
     *
     * @var string
     */
    protected $company = '';

    /**
     * customerNumber
     *
     * @var string
     */
    protected $customerNumber = '';

    /**
     * isValidated
     *
     * @var bool
     */
    protected $isValidated = FALSE;

    /**
     * validationHash
     *
     * @var string
     */
    protected $validationHash = '';

    /**
     * validationDate
     *
     * @var \DateTime
     */
    protected $validationDate = null;

    public function __construct()
    {
        if( ! $this->validationHash){
            $this->validationHash = \Medienreaktor\FormDoubleOptIn\Utility\Uuid::generate();
        }
    }

    /**
     * Returns the pagelanguage
     *
     * @return string $pagelanguage
     */
    public function getPagelanguage()
    {
        return $this->pagelanguage;
    }

    /**
     * Sets the pagelanguage
     *
     * @param string $pagelanguage
     * @return void
     */
    public function setPagelanguage($pagelanguage)
    {
        $this->pagelanguage = $pagelanguage;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the givenName
     *
     * @return string $givenName
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Sets the givenName
     *
     * @param string $givenName
     * @return void
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * Returns the familyName
     *
     * @return string $familyName
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Sets the familyName
     *
     * @param string $familyName
     * @return void
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * Returns the company
     *
     * @return string $company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company
     *
     * @param string $company
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Returns the customerNumber
     *
     * @return string $customerNumber
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }

    /**
     * Sets the customerNumber
     *
     * @param string $customerNumber
     * @return void
     */
    public function setCustomerNumber($customerNumber)
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * Returns isValidated
     *
     * @return bool $isValidated
     */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

    /**
     * Sets isValidated
     *
     * @param bool $isValidated
     * @return void
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;
    }

    /**
     * Returns the validationHash
     *
     * @return string $validationHash
     */
    public function getValidationHash()
    {
        return $this->validationHash;
    }

    /**
     * Sets the validationHash
     *
     * @param string $validationHash
     * @return void
     */
    public function setValidationHash($validationHash)
    {
        $this->validationHash = $validationHash;
    }

    /**
     * Returns the validationDate
     *
     * @return \DateTime $validationDate
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    /**
     * Sets the validationDate
     *
     * @param \DateTime $validationDate
     * @return void
     */
    public function setValidationDate(\DateTime $validationDate)
    {
        $this->validationDate = $validationDate;
    }

}
