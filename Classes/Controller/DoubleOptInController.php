<?php
namespace Medienreaktor\FormDoubleOptIn\Controller;

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

    /**
     * signalSlotDispatcher
     *
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    public function injectOptInRepository(\Medienreaktor\FormDoubleOptIn\Domain\Repository\OptInRepository $optInRepository): void
    {
        $this->optInRepository = $optInRepository;
    }

    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $dispatcher): void
    {
        $this->signalSlotDispatcher = $dispatcher;
    }

    /**
     * action validation
     *
     * @return void
     */
    public function validationAction()
    {
        $success = FALSE;

        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');
            $optIn = $this->optInRepository->findOneByValidationHash($hash);

            if ($optIn) {
                $optIn->setIsValidated(TRUE);
                $optIn->setValidationDate(new \DateTime);
                $this->optInRepository->update($optIn);

                $this->signalSlotDispatcher->dispatch(__CLASS__, 'afterOptInValidation', [$optIn]);

                $success = TRUE;
            }
        }

        $this->view->assign('success', $success);
    }
}
