<?php

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event before OptIn is deleted
 */
final class BeforeOptInDeletionEvent
{
    public function __construct(private readonly OptIn $optIn) {}

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }

}
