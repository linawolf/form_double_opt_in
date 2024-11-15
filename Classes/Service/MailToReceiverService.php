<?php

namespace LinaWolf\FormDoubleOptIn\Service;

use LinaWolf\FormDoubleOptIn\Domain\Model\OptIn;
use LinaWolf\FormDoubleOptIn\Utility\AddressUtility;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MailToReceiverService
{
    public function __construct(private readonly Mailer $mailer) {}

    public function sendPreparedMail(array $mailData): void
    {
        $bodyHTML = $mailData['bodyHTML'] ?? '';
        $bodyText = $mailData['bodyText'] ?? '';
        $subject = $mailData['subject'] ?? 'Default Subject';
        $senderAddress = $mailData['senderAddress'] ?? $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'] ?? 'default@example.org';
        $senderName = $mailData['senderName'] ?? $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'] ?? 'Default Sender';
        $recipientsArray = $mailData['recipientsArray'] ?? [];
        $replyToRecipientsArray = $mailData['replyToRecipientsArray'] ?? [];
        $carbonCopyRecipientsArray = $mailData['carbonCopyRecipientsArray'] ?? [];
        $blindCarbonCopyRecipientsArray = $mailData['blindCarbonCopyRecipientsArray'] ?? [];

        if (empty($recipientsArray)) {
            throw new \InvalidArgumentException('Recipients array cannot be empty.', 1731659857);
        }
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
            $newMail->cc(...AddressUtility::toAdresses($carbonCopyRecipientsArray));
        }
        if (!empty($blindCarbonCopyRecipientsArray)) {
            $newMail->bcc(...AddressUtility::toAdresses($blindCarbonCopyRecipientsArray));
        }
        $this->mailer->send($newMail);
    }

    public function sendNewMail(array $settings, OptIn $optIn): void
    {
        $notificationMailReceiverName = $settings['notificationMailReceiverName'] ?? false;
        $notificationMailSenderMail = $settings['notificationMailSenderMail'] ?? false;
        $notificationMailSenderName = $settings['notificationMailSenderName'] ?? false;
        $notificationMailSubject = $settings['notificationMailSubject'] ?? false;
        $notificationMailReceiverMail = $settings['notificationMailReceiverMail'] ?? false;

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
