<?php

namespace LinaWolf\FormDoubleOptIn\Domain\Finishers;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;
use LinaWolf\FormDoubleOptIn\Domain\Repository\OptInRepository;
use LinaWolf\FormDoubleOptIn\Event\AfterOptInCreationEvent;
use LinaWolf\FormDoubleOptIn\Utility\AddressUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;

class DoubleOptInFormFinisher extends EmailFinisher
{
    /**
     * optInRepository
     *
     * @var OptInRepository
     */
    protected $optInRepository;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function injectOptInRepository(OptInRepository $optInRepository): void
    {
        $this->optInRepository = $optInRepository;
    }

    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $pagelanguage = $context->getPropertyFromAspect('language', 'id');

        $title = $this->parseOption('title');
        $givenName = $this->parseOption('givenName');
        $familyName = $this->parseOption('familyName');
        $email = $this->parseOption('email');
        $company = $this->parseOption('company');
        $customerNumber = $this->parseOption('customerNumber');
        $validationPid = $this->parseOption('validationPid');

        $this->validateInput($email, $customerNumber, (int)$validationPid);

        $formRuntime = $this->finisherContext->getFormRuntime();

        $mailToReceiverBody = $this->prepareMailToReceiver($formRuntime);

        $optIn = $this->createOptInModel($pagelanguage, $title, $givenName, $familyName, $email, $company, $customerNumber, $mailToReceiverBody);

        $this->optInRepository->add($optIn);

        $this->eventDispatcher->dispatch(new AfterOptInCreationEvent($optIn));

        $persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $persistenceManager->persistAll();

        $this->sendDoubleOptInMail($formRuntime, $optIn, $validationPid);
    }

    /**
     * @param $pagelanguage
     * @param $title
     * @param $givenName
     * @param $familyName
     * @param $email
     * @param $company
     * @param $customerNumber
     * @return OptIn
     * @throws InvalidConfigurationTypeException
     */
    protected function createOptInModel(
        string $pagelanguage,
        string $title,
        string $givenName,
        string $familyName,
        string $email,
        string $company,
        string $customerNumber,
        string $mailToReceiverBody
    ): OptIn {
        $optIn = new OptIn();
        $optIn->setPagelanguage($pagelanguage);
        if (!empty($title)) {
            $optIn->setTitle($title);
        }
        if (!empty($givenName)) {
            $optIn->setGivenName($givenName);
        }
        if (!empty($familyName)) {
            $optIn->setFamilyName($familyName);
        }
        if (!empty($email)) {
            $optIn->setEmail($email);
        }
        if (!empty($company)) {
            $optIn->setCompany($company);
        }
        if (!empty($customerNumber)) {
            $optIn->setCustomerNumber($customerNumber);
        }
        $optIn->setMailBody($mailToReceiverBody);
        $optIn->setRegistrationDate(new \DateTime());

        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $storagePid = (int)$configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['storagePid'];
        if ($storagePid === 0) {
            throw new \Exception('The storagePid is not set. Please set it by ' .
                'TypoScript \'plugin.tx_formdoubleoptin_doubleoptin.persistence.storagePid\'.');
        }
        $optIn->setPid($storagePid);
        return $optIn;
    }

    /**
     * @throws FinisherException
     */
    private function validateInput(string $email, string $customerNumber, int $validationPid): void
    {
        if (empty($email) && empty($customerNumber)) {
            throw new FinisherException('The options "email" or "customerNumber" must be set.', 1_527_145_965);
        }

        if ($validationPid < 1) {
            throw new FinisherException('The option "validationPid" must be set.', 1_527_145_966);
        }
    }

    private function prepareMailToReceiver(FormRuntime $formRuntime): string
    {
        $recipients = $this->getRecipients('recipients');
        if (count($recipients) === 0) {
            return '';
        }

        $recipientsArray = AddressUtility::toArray($recipients);

        $addHtmlPart = $this->isAddHtmlPart();
        $subject = $this->parseOption('subjectReceiver');

        if (empty($subject)) {
            throw new FinisherException('The option "subjectReceiver" must be set for the DoubleOptInFormFinisher.', 1_327_060_320);
        }
        $mail = $this->initializeFluidEmail($formRuntime)
            ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
            ->assign('title', $subject);

        $bodyHTML = $mail->getHtmlBody(true);
        $bodyText = $mail->getTextBody(true);
        $json = array_merge($this->getAdresses(), compact('recipientsArray', 'subject', 'addHtmlPart', 'bodyHTML', 'bodyText'));

        return json_encode($json, JSON_THROW_ON_ERROR);
    }

    private function sendDoubleOptInMail(FormRuntime $formRuntime, OptIn $optIn, int $validationPid): void
    {
        $senderName = null;
        $languageBackup = null;
        $addHtmlPart = $this->isAddHtmlPart();

        $translationService = TranslationService::getInstance();
        if (isset($this->options['translation']['language']) && !empty($this->options['translation']['language'])) {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }
        $recipientAddress = $this->parseOption('email');
        extract($this->getAdresses());
        $subject = $this->parseOption('subject');
        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the DoubleOptInFormFinisher.', 1_327_060_320);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the DoubleOptInFormFinisher.', 1_327_060_210);
        }
        $mail = $this->initializeFluidEmail($formRuntime)
            ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
            ->assign('title', $subject)
            ->assign('optIn', $optIn)
            ->assign('validationPid', $validationPid);

        $doubleOpInTemplateName = $this->options['doubleOpInTemplateName'] ?? 'DoubleOptIn';
        $mail
            ->setTemplate($doubleOpInTemplateName);
        $mail
            ->from(new Address($senderAddress, $senderName))
            ->to($recipientAddress)
            ->subject($subject);

        if (!empty($replyToRecipients)) {
            $mail->replyTo(...$replyToRecipients);
        }
        if (!empty($carbonCopyRecipients)) {
            $mail->cc(...$carbonCopyRecipients);
        }
        if (!empty($blindCarbonCopyRecipients)) {
            $mail->bcc(...$blindCarbonCopyRecipients);
        }
        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }
        GeneralUtility::makeInstance(Mailer::class)->send($mail);
    }

    /**
     * @return bool
     */
    private function isAddHtmlPart(): bool
    {
        // Flexform overrides write strings instead of integers so
        // we need to cast the string '0' to false.
        if (
            isset($this->options['addHtmlPart'])
            && $this->options['addHtmlPart'] === '0'
        ) {
            $this->options['addHtmlPart'] = false;
        }
        $addHtmlPart = $this->parseOption('addHtmlPart') ? true : false;
        return $addHtmlPart;
    }

    /**
     * @return array
     */
    private function getAdresses(): array
    {
        $senderAddress = $this->parseOption('senderAddress');
        $senderAddress = is_string($senderAddress) ? $senderAddress : '';
        $senderName = $this->parseOption('senderName');
        $senderName = is_string($senderName) ? $senderName : '';
        $replyToRecipients = $this->getRecipients('replyToRecipients');
        $replyToRecipientsArray = AddressUtility::toArray($replyToRecipients);
        $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients');
        $carbonCopyRecipientsArray = AddressUtility::toArray($carbonCopyRecipients);
        $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients');
        $blindCarbonCopyRecipientsArray = AddressUtility::toArray($blindCarbonCopyRecipients);
        return compact('senderAddress', 'senderName', 'replyToRecipients', 'carbonCopyRecipients', 'blindCarbonCopyRecipients');
    }
}
