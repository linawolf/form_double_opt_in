<?php
namespace Medienreaktor\FormDoubleOptIn\Event;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;

/**
 * Event after OptIn record has been validated.
 */
final class AfterOptInValidationEvent
{

    /**
     * @var \Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn
     */
     private $optIn;

     public function __construct(\Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn $optIn)
     {
         $this->optIn = $optIn;
     }

     public function getOptIn(): \Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn
     {
         return $this->optIn;
     }
}
