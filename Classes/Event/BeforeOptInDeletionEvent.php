<?php

declare(strict_types=1);

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event before OptIn is deleted
 */
final readonly class BeforeOptInDeletionEvent
{
    public function __construct(private OptIn $optIn) {}

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }

}
