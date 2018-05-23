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
     * @inject
     */
    protected $optInRepository = NULL;

    /**
     * action validation
     *
     * @return void
     */
    public function validationAction()
    {
        if ($this->request->hasArgument('hash')) {
            $hash = $this->request->getArgument('hash');
            $optIn = $this->optInRepository->findByValidationHash($hash);

            $optIn->setIsValidated(TRUE);
            $optIn->setValidationDate(new \DateTime);

            $this->optInRepository->update($optIn);

            $this->view->assign('success', TRUE);
        } else {
            $this->view->assign('success', FALSE);
        }
    }
}
