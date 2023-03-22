<?php
namespace Medienreaktor\FormDoubleOptIn\Domain\Finishers;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use Medienreaktor\FormDoubleOptIn\Event\AfterOptInCreationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;

class DoubleOptInFormFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    const FORMAT_PLAINTEXT = 'plaintext';

    /**
     * optInRepository
     *
     * @var \Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository
     */
    protected $optInRepository;

    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function injectOptInRepository(\Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository $optInRepository): void
    {
        $this->optInRepository = $optInRepository;
    }

    public function injectEventDispatcher(\Psr\EventDispatcher\EventDispatcherInterface $eventDispatcher): void
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
        $context = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
        $pagelanguage = $context->getPropertyFromAspect('language', 'id');

        $title = $this->parseOption('title');
        $givenName = $this->parseOption('givenName');
        $familyName = $this->parseOption('familyName');
        $email = $this->parseOption('email');
        $company = $this->parseOption('company');
        $customerNumber = $this->parseOption('customerNumber');
        $validationPid = $this->parseOption('validationPid');

        $this->validateInput($email, $customerNumber, $validationPid);

        $formRuntime = $this->finisherContext->getFormRuntime();

        $mailToReceiverBody = $this->prepareMailToReceiver($formRuntime);

        $optIn = $this->createOptInModel($pagelanguage, $title, $givenName, $familyName, $email, $company,
            $customerNumber, $mailToReceiverBody);

        $this->optInRepository->add($optIn);

        $this->eventDispatcher->dispatch(new AfterOptInCreationEvent($optIn));

        $persistenceManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager::class);
        $persistenceManager->persistAll();

        $this->sendDoubleOptinMail($formRuntime, $optIn, $validationPid);
    }

    private function prepareMailToReceiver(FormRuntime $formRuntime): string
    {
        $format = $this->parseOption('format');
        $standaloneView = $this->initializeStandaloneView($formRuntime, $format);
        $standaloneView->setTemplatePathAndFilename('EXT:form_double_opt_in/Resources/Private/Templates/Email/MailToReceiver.html');
        return $standaloneView->render();
    }

    /**
     * @param $email
     * @param $customerNumber
     * @param $validationPid
     * @return void
     * @throws FinisherException
     */
    private function validateInput($email, $customerNumber, $validationPid): void
    {
        if (empty($email) && empty($customerNumber)) {
            throw new FinisherException('The options "email" or "customerNumber" must be set.', 1527145965);
        }

        if (empty($validationPid)) {
            throw new FinisherException('The option "validationPid" must be set.', 1527145966);
        }
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
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function createOptInModel(
        $pagelanguage,
        $title,
        $givenName,
        $familyName,
        $email,
        $company,
        $customerNumber,
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

    private function sendDoubleOptinMail(FormRuntime $formRuntime, OptIn $optIn, int $validationPid): void
    {
        $standaloneView = $this->initializeStandaloneView($formRuntime, 'text');
        $standaloneView->assign('optIn', $optIn);
        $standaloneView->assign('validationPid', $validationPid);

        $translationService = TranslationService::getInstance();
        if (isset($this->options['translation']['language']) && !empty($this->options['translation']['language'])) {
            $languageBackup = $translationService->getLanguage();
            $translationService->setLanguage($this->options['translation']['language']);
        }
        $message = $standaloneView->render();
        if (!empty($languageBackup)) {
            $translationService->setLanguage($languageBackup);
        }

        $subject = $this->parseOption('subject');
        $recipientAddress = $this->parseOption('email');
        $recipientName = trim($this->parseOption('givenName') . ' ' . $this->parseOption('familyName'));
        $senderAddress = $this->parseOption('senderAddress');
        $senderName = $this->parseOption('senderName');
        $replyToAddress = $this->parseOption('replyToAddress');
        $carbonCopyAddress = $this->parseOption('carbonCopyAddress');
        $blindCarbonCopyAddress = $this->parseOption('blindCarbonCopyAddress');
        $format = $this->parseOption('format');

        if (empty($subject)) {
            throw new FinisherException('The option "subject" must be set for the EmailFinisher.', 1327060320);
        }
        if (empty($recipientAddress)) {
            throw new FinisherException('The option "recipientAddress" must be set for the EmailFinisher.', 1327060200);
        }
        if (empty($senderAddress)) {
            throw new FinisherException('The option "senderAddress" must be set for the EmailFinisher.', 1327060210);
        }

        $mail = $this->objectManager->get(MailMessage::class);

        $mail->setFrom([$senderAddress => $senderName])
            ->setTo([$recipientAddress => $recipientName])
            ->setSubject($subject);

        if (!empty($replyToAddress)) {
            $mail->setReplyTo($replyToAddress);
        }

        if (!empty($carbonCopyAddress)) {
            $mail->setCc($carbonCopyAddress);
        }

        if (!empty($blindCarbonCopyAddress)) {
            $mail->setBcc($blindCarbonCopyAddress);
        }

        if ($format === self::FORMAT_PLAINTEXT) {
            $mail->text($message);
        } else {
            $mail->html($message);
        }

        $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();

        $mail->send();
    }
}
