<?php

namespace Medienreaktor\FormDoubleOptIn\Controller;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository;
use Medienreaktor\FormDoubleOptIn\Event\AfterOptInValidationEvent;
use Medienreaktor\FormDoubleOptIn\Service\MailToReceiverService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/*
 * DoubleOptInController
 */
class DoubleOptInController extends ActionController
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
        $success = false;
        $validated = false;

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
                            $this->mailToReceiverService->sendPreparedMail(json_decode($optIn->getMailBody(), true, 512, JSON_THROW_ON_ERROR));
                        }
                    } else {
                        // Simple notification e-mail
                        $this->mailToReceiverService->sendNewMail($this->settings, $optIn);
                    }
                }

                $this->eventDispatcher->dispatch(new AfterOptInValidationEvent($optIn));

                if (!$isAlreadyValidated) {
                    $success = true;
                    if ($this->settings['deleteOptInRecordsAfterOptIn']) {
                        $this->optInRepository->remove($optIn);
                    } else {
                        // Set as validated in the db
                        $optIn->setIsValidated(true);
                        $optIn->setValidationDate(new \DateTime());
                        $this->optInRepository->update($optIn);
                    }
                    $this->signalSlotDispatcher->dispatch(self::class, 'afterOptInValidation', [$optIn]);
                }

                // If already validated
                if ($isAlreadyValidated) {
                    $validated = true;
                }
            }
        }

        $this->view->assign('success', $success);
        $this->view->assign('validated', $validated);
    }

    public function deleteAction()
    {
        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');
            /** @var ?OptIn $optIn */
            $optIn = $this->optInRepository->findOneByValidationHash($hash);
            if ($optIn !== null) {
                $this->optInRepository->remove($optIn);
            }
        }
    }
}
