<?php

namespace App\Services;

use Webklex\IMAP\Facades\Client;

class EmailService {
    // ... existing code ...

    public function getEmails($folder)
    {
        $client = Client::account('default');
        $client->connect();

        $inbox = $client->getFolder($folder);
        $messages = $inbox->messages()->all()->get();

        $emails = [];
        foreach ($messages as $message) {
            // معالجة التاريخ بشكل صحيح
            $date = $message->getDate();
            $formattedDate = $date ? date('Y-m-d H:i:s', strtotime($date)) : null;

            // معالجة عنوان البريد الإلكتروني بشكل آمن
            $fromAddress = $message->getFrom();
            $from = !empty($fromAddress) ? $fromAddress[0]->mail : null;

            // معالجة عنوان الرد بشكل آمن
            $replyTo = $message->getReplyTo();
            $replyToAddress = !empty($replyTo) ? $replyTo[0]->mail : null;

            $emails[] = [
                'id' => $message->getUid(),
                'subject' => $message->getSubject() ?? '',
                'from' => $from,
                'date' => $formattedDate,
                'body' => $this->getCleanBody($message),
                'is_read' => $message->getFlags('seen')->contains('seen'),
                'reply_to' => $replyToAddress,
            ];
        }

        return $emails;
    }

    // إضافة دالة مساعدة لتنظيف محتوى الرسالة
    private function getCleanBody($message) {
        // محاولة الحصول على نص HTML أولاً
        $body = $message->getHTMLBody();

        // إذا لم يكن هناك محتوى HTML، استخدم النص العادي
        if (empty($body)) {
            $body = $message->getTextBody();
        }

        // إزالة علامات HTML إذا كان ذلك مطلوباً
        $body = strip_tags($body);

        return $body ?? '';
    }


    public function getEmailById($id) {
        $client = Client::account('default');
        $client->connect();

        $inbox = $client->getFolder('INBOX');
        $message = $inbox->messages()->getMessageByUid($id);

        return [
            'id' => $message->getUid(),
            'subject' => $message->getSubject(),
            'from' => $message->getFrom()[0]->mail,
            'date' => $message->getDate(),
            'body' => $message->getHTMLBody(),
            'is_read' => $message->getFlags()->contains('\Seen'),
            'reply_to' => $message->getReplyTo()[0]->mail ?? null,
        ];
    }

    public function saveSentEmail($data) {
        $client = Client::account('default');
        $client->connect();

        // الحصول على مجلد Sent
        $sentFolder = $client->getFolder('Sent');

        // إنشاء رسالة جديدة
        $message = "To: {$data['to']}\r\n";
        $message .= "Subject: {$data['subject']}\r\n";
        $message .= "From: " . config('mail.from.address') . "\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $message .= $data['body'];

        // حفظ الرسالة في مجلد Sent
        $sentFolder->appendMessage($message, ['\Seen']);
    }
}
