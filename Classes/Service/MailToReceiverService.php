<?php

namespace Medienreaktor\FormDoubleOptIn\Service;

use Medienreaktor\FormDoubleOptIn\Domain\Model\OptIn;
use Medienreaktor\FormDoubleOptIn\Utility\AddressUtility;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;

class MailToReceiverService
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPreparedMail(array $mailData): void
    {
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

        $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Mail\MailMessage::class);
        if ($notificationMailSenderMail && $notificationMailSenderName) {
            $mail->from(new \Symfony\Component\Mime\Address($notificationMailSenderMail, $notificationMailSenderName));
        } elseif ($notificationMailSenderMail) {
            $mail->from(new \Symfony\Component\Mime\Address($notificationMailSenderMail));
        }
        if ($notificationMailReceiverMail && $notificationMailReceiverName) {
            $mail->to(new \Symfony\Component\Mime\Address($notificationMailReceiverMail, $notificationMailReceiverName));
        } elseif ($notificationMailReceiverMail) {
            $mail->to(new \Symfony\Component\Mime\Address($notificationMailReceiverMail));
        }
        if ($notificationMailSubject) {
            $mail->subject($notificationMailSubject);
        }
        $mail->text($language .', '. $title .', '. $givenName .', '. $familyName .', '.$email.', '.$company.', '.$customerNumber);
        $mail->html($language .'<br />'. $title .'<br />'. $givenName .'<br />'. $familyName .'<br />'.$email.'<br />'.$company.'<br />'.$customerNumber);
        $mail->send();
    }
}
