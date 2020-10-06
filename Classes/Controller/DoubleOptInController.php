<?php
namespace Medienreaktor\FormDoubleOptIn\Controller;

use Medienreaktor\FormDoubleOptIn\Event\AfterOptInValidationEvent;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * DoubleOptInController
 */
class DoubleOptInController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * optInRepository
     *
     * @var \Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository
     */
    protected $optInRepository;

    public function injectOptInRepository(\Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository $optInRepository): void
    {
        $this->optInRepository = $optInRepository;
    }

    /**
     * Validate the OptIn record
     */
    public function validationAction()
    {
        $success = FALSE;
        $validated = FALSE;
        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');
            $optIn = $this->optInRepository->findOneByValidationHash($hash);

            if ($optIn) {
                $isValidated = $optIn->getIsValidated();

            // Simple notification e-mail
                $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);
                $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
                $notificationMailEnable = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailEnable'];
                $notificationMailReceiverMail = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailReceiverMail'];

                if (!$isValidated && $notificationMailEnable && $notificationMailReceiverMail) {
                    $notificationMailReceiverName = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailReceiverName'];
                    $notificationMailSenderMail = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailSenderMail'];
                    $notificationMailSenderName = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailSenderName'];
                    $notificationMailSubject = $configuration['plugin.']['tx_formdoubleoptin_doubleoptin.']['persistence.']['notificationMailSubject'];

                    $language = $optIn->getPagelanguage();
                    $title = $optIn->getTitle();
                    $givenName = $optIn->getGivenName();
                    $familyName = $optIn->getFamilyName();
                    $email = $optIn->getEmail();
                    $company = $optIn->getCompany();
                    $customerNumber = $optIn->getCustomerNumber();

                    $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
                    if ($notificationMailSenderMail && $notificationMailSenderName) {
                        $mail->from(new \Symfony\Component\Mime\Address($notificationMailSenderMail, $notificationMailSenderName));
                    } elseif ($notificationMailSenderMail) {
                        $mail->from(new \Symfony\Component\Mime\Address($notificationMailSenderMail));
                    }
                    if ($notificationMailReceiverMail && $notificationMailReceiverName) {
                        $mail->to(new \Symfony\Component\Mime\Address($notificationMailReceiverMail, $notificationMailReceiverName));
                    } elseif ($notificationMailReceiverMail) {
                        $mail->to(new \Symfony\Component\Mime\Address($notificationMailReceiverMail));
                    }
                    if ($notificationMailSubject) {
                        $mail->subject($notificationMailSubject);
                    }
                    $mail->text($language .', '. $title .', '. $givenName .', '. $familyName .', '.$email.', '.$company.', '.$customerNumber);
                    $mail->html($language .'<br />'. $title .'<br />'. $givenName .'<br />'. $familyName .'<br />'.$email.'<br />'.$company.'<br />'.$customerNumber);
                    $mail->send();
                }

            // Set validated if not yet
                if (!$isValidated) {
                    $optIn->setIsValidated(TRUE);
                    $optIn->setValidationDate(new \DateTime);
                    $this->optInRepository->update($optIn);
                    $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInValidation', [$optIn]);
                    $success = TRUE;
                }

            // If already validated
                if ($isValidated) {
                    $validated = TRUE;
                }
            }
        }

        $this->view->assign('success', $success);
        $this->view->assign('validated', $validated);
    }
}
