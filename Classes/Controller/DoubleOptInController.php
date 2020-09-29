<?php
namespace Medienreaktor\FormDoubleOptIn\Controller;

use Medienreaktor\FormDoubleOptIn\Event\AfterOptInValidationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

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

                $this->eventDispatcher->dispatch(new AfterOptInValidationEvent($optIn));

                $success = TRUE;
            }
        }

        $this->view->assign('success', $success);
    }
}
