<?php
namespace Medienreaktor\FormDoubleOptIn\Controller;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository;
use Medienreaktor\FormDoubleOptIn\Event\AfterOptInValidationEvent;
use Medienreaktor\FormDoubleOptIn\Service\MailToReceiverService;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * DoubleOptInController
 */
class DoubleOptInController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    private MailToReceiverService $mailToReceiverService;
    protected OptInRepository $optInRepository;

    public function __construct(MailToReceiverService $mailToReceiverService, OptInRepository $optInRepository)
    {
        $this->mailToReceiverService = $mailToReceiverService;
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

            /** @var OptIn $optIn */
            $optIn = $this->optInRepository->findOneByValidationHash($hash);

            if ($optIn) {
                $isAlreadyValidated = $optIn->getIsValidated();

                $notificationMailEnable = $this->settings['notificationMailEnable'] ?? false;
                $usePreparedEmail = $this->settings['usePreparedEmail'] ?? false;

                if (!$isAlreadyValidated && $notificationMailEnable) {
                    if ($usePreparedEmail) {
                        // Prepared e-mail with full power of the form extension
                        if ($optIn->getMailBody() !== '') {
                            $this->mailToReceiverService->sendPreparedMail(json_decode($optIn->getMailBody(), true));
                        }
                    } else {
                        // Simple notification e-mail
                        $this->mailToReceiverService->sendNewMail($this->settings, $optIn);
                    }
                }
                
                $this->eventDispatcher->dispatch(new AfterOptInValidationEvent($optIn));

                // Set validated if not yet
                if (!$isAlreadyValidated) {
                    $optIn->setIsValidated(TRUE);
                    $optIn->setValidationDate(new \DateTime);
                    $this->optInRepository->update($optIn);
                    $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInValidation', [$optIn]);
                    $success = TRUE;
                }

                // If already validated
                if ($isAlreadyValidated) {
                    $validated = TRUE;
                }
            }
        }

        $this->view->assign('success', $success);
        $this->view->assign('validated', $validated);
    }
}
