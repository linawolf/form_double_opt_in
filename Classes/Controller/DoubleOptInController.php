<?php

namespace LinaWolf\FormDoubleOptIn\Controller;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;
use LinaWolf\FormDoubleOptIn\Domain\Repository\OptInRepository;
use LinaWolf\FormDoubleOptIn\Event\AfterOptInValidationEvent;
use LinaWolf\FormDoubleOptIn\Event\BeforeOptInDeletionEvent;
use LinaWolf\FormDoubleOptIn\Service\MailToReceiverService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/*
 * DoubleOptInController
 */
class DoubleOptInController extends ActionController
{
    public function __construct(private readonly MailToReceiverService $mailToReceiverService, protected OptInRepository $optInRepository) {}

    /**
     * Validate the OptIn record
     */
    public function validationAction(): ResponseInterface
    {
        $success = false;
        $validated = false;

        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');

            $optIn = $this->optInRepository->findOneBy(['validationHash' => $hash]);

            if ($optIn instanceof OptIn) {
                $isAlreadyValidated = $optIn->isValidated();

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
                        $event = new BeforeOptInDeletionEvent($optIn);
                        $this->eventDispatcher->dispatch($event);
                        $this->optInRepository->remove($optIn);
                    } else {
                        // Set as validated in the db
                        $optIn->setIsValidated(true);
                        $optIn->setValidationDate(new \DateTime());
                        $this->optInRepository->update($optIn);
                    }
                }

                // If already validated
                if ($isAlreadyValidated) {
                    $validated = true;
                }
            }
        }

        $this->view->assign('success', $success);
        $this->view->assign('validated', $validated);
        return $this->htmlResponse();
    }

    public function deleteAction(): ResponseInterface
    {
        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');
            /** @var ?OptIn $optIn */
            $optIn = $this->optInRepository->findOneBy(['validationHash' => $hash]);
            if ($optIn !== null) {
                $this->optInRepository->remove($optIn);
            }
        }
        return $this->htmlResponse();
    }
}
