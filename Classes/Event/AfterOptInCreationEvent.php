<?php

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event after OptIn record has been created.
 */
final class AfterOptInCreationEvent
{
    public function __construct(private readonly OptIn $optIn)
    {
    }

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }
}
