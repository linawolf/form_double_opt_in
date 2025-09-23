<?php

namespace LinaWolf\FormDoubleOptIn\Event;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;

final class RedirectToConfirmationFormEvent
{
    public function __construct(
        private readonly OptIn $optIn,
        private readonly int $validationPid,
    ) {}

    public function getOptIn(): OptIn
    {
        return $this->optIn;
    }

    public function getValidationPid(): int
    {
        return $this->validationPid;
    }
}
