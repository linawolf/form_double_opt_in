<?php

namespace Medienreaktor\FormDoubleOptIn\Service;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use Medienreaktor\FormDoubleOptIn\Utility\AddressUtility;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MailToReceiverService
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPreparedMail(array $mailData): void
    {
        $bodyHTML = null;
        $bodyText = null;
        $subject = null;
        $senderAddress = null;
        $senderName = null;
        $recipientsArray = null;
        $mail = null;
        extract($mailData);
        $newMail = new Email();
        $newMail->html($bodyHTML)
            ->text($bodyText)
            ->subject($subject)
            ->from(new Address($senderAddress, $senderName))
            ->to(...AddressUtility::toAdresses($recipientsArray));
        if (!empty($replyToRecipientsArray)) {
            $newMail->replyTo(...AddressUtility::toAdresses($replyToRecipientsArray));
        }
        if (!empty($carbonCopyRecipientsArray)) {
            $mail->cc(...AddressUtility::toAdresses($carbonCopyRecipientsArray));
        }
        if (!empty($blindCarbonCopyRecipientsArray)) {
            $mail->bcc(...AddressUtility::toAdresses($blindCarbonCopyRecipientsArray));
        }
        $this->mailer->send($newMail);
    }

    public function sendNewMail(array $settings, OptIn $optIn): void
    {
        $notificationMailReceiverName = $settings['notificationMailReceiverName'];
        $notificationMailSenderMail = $settings['notificationMailSenderMail'];
        $notificationMailSenderName = $settings['notificationMailSenderName'];
        $notificationMailSubject = $settings['notificationMailSubject'];
        $notificationMailReceiverMail = $settings['notificationMailReceiverMail'];

        $language = $optIn->getPagelanguage();
        $title = $optIn->getTitle();
        $givenName = $optIn->getGivenName();
        $familyName = $optIn->getFamilyName();
        $email = $optIn->getEmail();
        $company = $optIn->getCompany();
        $customerNumber = $optIn->getCustomerNumber();

        $mail = GeneralUtility::makeInstance(MailMessage::class);
        if ($notificationMailSenderMail && $notificationMailSenderName) {
            $mail->from(new Address($notificationMailSenderMail, $notificationMailSenderName));
        } elseif ($notificationMailSenderMail) {
            $mail->from(new Address($notificationMailSenderMail));
        }
        if ($notificationMailReceiverMail && $notificationMailReceiverName) {
            $mail->to(new Address($notificationMailReceiverMail, $notificationMailReceiverName));
        } elseif ($notificationMailReceiverMail) {
            $mail->to(new Address($notificationMailReceiverMail));
        }
        if ($notificationMailSubject) {
            $mail->subject($notificationMailSubject);
        }
        $mail->text($language . ', ' . $title . ', ' . $givenName . ', ' . $familyName . ', ' . $email . ', ' . $company . ', ' . $customerNumber);
        $mail->html($language . '<br />' . $title . '<br />' . $givenName . '<br />' . $familyName . '<br />' . $email . '<br />' . $company . '<br />' . $customerNumber);
        $mail->send();
    }
}
