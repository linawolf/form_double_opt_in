<?php

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event after OptIn record has been validated.
 */
final class AfterOptInValidationEvent
{
    public function __construct(private readonly OptIn $optIn) {}

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }
}
