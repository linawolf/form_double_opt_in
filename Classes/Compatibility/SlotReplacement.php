<?php

namespace Medienreaktor\FormDoubleOptIn\Compatibility;

use Medienreaktor\FormDoubleOptIn\Controller\DoubleOptInController;
use Medienreaktor\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher;
use Medienreaktor\FormDoubleOptIn\Event\AfterOptInCreationEvent;
use Medienreaktor\FormDoubleOptIn\Event\AfterOptInValidationEvent;

class SlotReplacement
{
    /**
     * signalSlotDispatcher
     *
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     */
    protected $signalSlotDispatcher;

    public function injectSignalSlotDispatcher(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher $dispatcher): void
    {
        $this->signalSlotDispatcher = $dispatcher;
    }

    public function onOptInRecordCreated(AfterOptInCreationEvent $event): void
    {
        $this->signalSlotDispatcher->dispatch(DoubleOptInFormFinisher::class, 'afterOptInCreation', [$event->getOptIn()]);
    }

    public function onOptInRecordValidated(AfterOptInValidationEvent $event): void
    {
        $this->signalSlotDispatcher->dispatch(DoubleOptInController::class, 'afterOptInValidation', [$event->getOptIn()]);
    }
}
