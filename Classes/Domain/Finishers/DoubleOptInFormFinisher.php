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
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Mail\TemplatedEmailFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;

final class DoubleOptInFormFinisher extends EmailFinisher
{
    public function __construct(
        private readonly OptInRepository $optInRepository,
        protected readonly EventDispatcherInterface $eventDispatcher,
        protected readonly TemplatedEmailFactory $templatedEmailFactory,
        protected readonly MailerInterface $mailer,
        private readonly ConfigurationManager $configurationManager,
        private readonly PersistenceManager $persistenceManager,
        private readonly Context $context) {

    }

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal(): void
    {
        $context = $this->context;
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

        $this->persistenceManager->persistAll();

        $this->sendDoubleOptInMail($formRuntime, $optIn, $validationPid);

        $this->finisherContext->getFinisherVariableProvider()->add(
            $this->shortFinisherIdentifier,
            'optInRecordUid',
            $optIn->getUid(),
        );
    }

    private function createOptInModel(
        string $pagelanguage,
        string $title,
        string $givenName,
        string $familyName,
        string $email,
        string $company,
        string $customerNumber,
        string $mailToReceiverBody,
    ): OptIn {
        $optIn = new OptIn();
        $optIn->setPagelanguage($pagelanguage);
        if ($title !== '' && $title !== '0') {
            $optIn->setTitle($title);
        }
        if ($givenName !== '' && $givenName !== '0') {
            $optIn->setGivenName($givenName);
        }
        if ($familyName !== '' && $familyName !== '0') {
            $optIn->setFamilyName($familyName);
        }
        if ($email !== '' && $email !== '0') {
            $optIn->setEmail($email);
        }
        if ($company !== '' && $company !== '0') {
            $optIn->setCompany($company);
        }
        if ($customerNumber !== '' && $customerNumber !== '0') {
            $optIn->setCustomerNumber($customerNumber);
        }
        $optIn->setMailBody($mailToReceiverBody);
        $optIn->setRegistrationDate(new \DateTime());

        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $storagePid = (int)$configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['storagePid'];
        if ($storagePid === 0) {
            throw new \Exception('The storagePid is not set. Please set it by ' .
                'TypoScript \'plugin.tx_formdoubleoptin_doubleoptin.persistence.storagePid\'.', 6259273299);
        }
        $optIn->setPid($storagePid);
        return $optIn;
    }

    /**
     * @throws FinisherException
     */
    private function validateInput(string $email, string $customerNumber, int $validationPid): void
    {
        if (($email === '' || $email === '0') && ($customerNumber === '' || $customerNumber === '0')) {
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
        $json = array_merge($this->getAdresses(), ['recipientsArray' => $recipientsArray, 'subject' => $subject, 'addHtmlPart' => $addHtmlPart, 'bodyHTML' => $bodyHTML, 'bodyText' => $bodyText]);

        return json_encode($json, JSON_THROW_ON_ERROR);
    }

    private function sendDoubleOptInMail(FormRuntime $formRuntime, OptIn $optIn, int $validationPid): void
    {
        $addHtmlPart = $this->isAddHtmlPart();

        $translationService = GeneralUtility::makeInstance(TranslationService::class);
        $languageBackup = null;

        if (!empty($this->options['translation']['language'])) {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }

        $recipientAddress = $this->parseOption('email');

        // Replace `extract` with explicit variable assignments
        $addresses = $this->getAdresses();
        $senderAddress = $addresses['senderAddress'] ?? null;
        $senderName = $addresses['senderName'] ?? null;
        $replyToRecipients = $addresses['replyToRecipients'] ?? [];
        $carbonCopyRecipients = $addresses['carbonCopyRecipients'] ?? [];
        $blindCarbonCopyRecipients = $addresses['blindCarbonCopyRecipients'] ?? [];

        $subject = $this->parseOption('subject');
        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the DoubleOptInFormFinisher.', 1_327_060_320);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the DoubleOptInFormFinisher.', 1_327_060_210);
        }

        $useFormLinkInEmail = $this->parseOption('useFormLinkInEmail');

        $mail = $this->initializeFluidEmail($formRuntime)
            ->format($addHtmlPart ? FluidEmail::FORMAT_BOTH : FluidEmail::FORMAT_PLAIN)
            ->assign('title', $subject)
            ->assign('useFormLinkInEmail', $useFormLinkInEmail)
            ->assign('optIn', $optIn)
            ->assign('validationPid', $validationPid);

        $doubleOpInTemplateName = $this->options['doubleOpInTemplateName'] ?? 'DoubleOptIn';
        $mail->setTemplate($doubleOpInTemplateName);
        $mail->from(new Address($senderAddress, $senderName))
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
        return (bool)$this->parseOption('addHtmlPart');
    }

    private function getAdresses(): array
    {
        $senderAddress = $this->parseOption('senderAddress');
        $senderAddress = is_string($senderAddress) ? $senderAddress : '';
        $senderName = $this->parseOption('senderName');
        $senderName = is_string($senderName) ? $senderName : '';
        $replyToRecipients = $this->getRecipients('replyToRecipients');
        AddressUtility::toArray($replyToRecipients);
        $carbonCopyRecipients = $this->getRecipients('carbonCopyRecipients');
        AddressUtility::toArray($carbonCopyRecipients);
        $blindCarbonCopyRecipients = $this->getRecipients('blindCarbonCopyRecipients');
        AddressUtility::toArray($blindCarbonCopyRecipients);
        return ['senderAddress' => $senderAddress, 'senderName' => $senderName, 'replyToRecipients' => $replyToRecipients, 'carbonCopyRecipients' => $carbonCopyRecipients, 'blindCarbonCopyRecipients' => $blindCarbonCopyRecipients];
    }
}
