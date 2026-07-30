<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event after OptIn record has been created.
 */
final readonly class AfterOptInCreationEvent
{
    public function __construct(private OptIn $optIn) {}

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }
}
