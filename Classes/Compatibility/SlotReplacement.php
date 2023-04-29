<?php

namespace LinaWolf\FormDoubleOptIn\Compatibility;

use LinaWolf\FormDoubleOptIn\Controller\DoubleOptInController;
use LinaWolf\FormDoubleOptIn\Domain\Finishers\DoubleOptInFormFinisher;
use LinaWolf\FormDoubleOptIn\Event\AfterOptInCreationEvent;
use LinaWolf\FormDoubleOptIn\Event\AfterOptInValidationEvent;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class SlotReplacement
{
    /**
     * signalSlotDispatcher
     *
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    public function injectSignalSlotDispatcher(Dispatcher $dispatcher): void
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
