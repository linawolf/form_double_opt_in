<?php
namespace Medienreaktor\FormDoubleOptIn\Domain\Finishers;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
use TYPO3\CMS\Form\Service\TranslationService;

class DoubleOptInFormFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    /**
     * optInRepository
     *
     * @var \Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository
     * @inject
     */
    protected $optInRepository = NULL;

    /**
     * signalSlotDispatcher
     *
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @inject
     */
    protected $signalSlotDispatcher = NULL;

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @throws FinisherException
     */
    protected function executeInternal()
    {
        $givenName = $this->parseOption('givenName');
        $familyName = $this->parseOption('familyName');
        $email = $this->parseOption('email');
        $company = $this->parseOption('company');
        $customerNumber = $this->parseOption('customerNumber');
        $validationPid = $this->parseOption('validationPid');

        if (empty($email) && empty($customerNumber)) {
            throw new FinisherException('The options "email" or "customerNumber" must be set.', 1527145965);
        }

        if (empty($validationPid)) {
            throw new FinisherException('The option "validationPid" must be set.', 1527145966);
        }

        $formRuntime = $this->finisherContext->getFormRuntime();
        $standaloneView = $this->initializeStandaloneView($formRuntime);

        $optIn = new OptIn();
        if(!empty($givenName)) {
            $optIn->setGivenName($givenName);
        }
        if(!empty($familyName)) {
            $optIn->setFamilyName($familyName);
        }
        if(!empty($email)) {
            $optIn->setEmail($email);
        }
        if(!empty($company)) {
            $optIn->setCompany($company);
        }
        if(!empty($customerNumber)) {
            $optIn->setCustomerNumber($customerNumber);
        }

        $this->configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $storagePid = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['storagePid'];
        $optIn->setPid($storagePid);

        $this->optInRepository->add($optIn);

        $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInCreation', [$optIn]);

        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $persistenceManager->persistAll();

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
        $recipientAddress = $this->parseOption('recipientAddress');
        $recipientName = $this->parseOption('recipientName');
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
            $mail->setBody($message, 'text/plain');
        } else {
            $mail->setBody($message, 'text/html');
        }

        $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();

        $mail->send();
    }
}
