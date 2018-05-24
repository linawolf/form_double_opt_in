<?php
namespace Medienreaktor\FormDoubleOptIn\Domain\Finishers;

class DoubleOptInFormFinisher extends \TYPO3\CMS\Form\Domain\Finishers\EmailFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = [
        'recipientName' => '',
        'senderName' => '',
        'format' => self::FORMAT_HTML,
        'attachUploads' => false
    ];

}
