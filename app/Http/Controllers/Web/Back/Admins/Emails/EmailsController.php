<?php

namespace App\Http\Controllers\Web\Back\Admins\Emails;

use App\Http\Controllers\Controller;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailsController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index($folder)
    {
        if($folder == 'sent'){
            $folder = 'INBOX/Sent';
        } elseif($folder == 'trash'){
            $folder = 'INBOX/Trash';
        }elseif($folder == 'drafts'){
            $folder = 'INBOX/Drafts';
        } elseif($folder == 'junk'){
            $folder = 'INBOX/Junk';
        } elseif($folder == 'inbox'){
            $folder = 'INBOX';
        }

        $emails = $this->emailService->getEmails($folder);
        return $emails;
        return view('back.admins.emails.index', compact('emails'));
    }

    public function show(Request $request)
    {
        $id = decrypt($request->id);
        $email = $this->emailService->getEmailById($id);
        return view('back.admins.emails.show', compact('email'));
    }

    public function sendReply(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $originalEmail = $this->emailService->getEmailById(decrypt($request->id));

        // إرسال البريد الإلكتروني
        Mail::raw($request->message, function ($mail) use ($originalEmail) {
            $mail->to($originalEmail['from'])
                ->subject("Re: " . $originalEmail['subject'])
                ->from(config('mail.from.address')); // إضافة عنوان المرسل
        });

        // حفظ الرسالة في مجلد Sent
        $this->emailService->saveSentEmail([
            'to' => $originalEmail['from'],
            'subject' => "Re: " . $originalEmail['subject'],
            'body' => $request->message,
            'original_id' => decrypt($request->id)
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }
}
