<?php

namespace App\Http\Controllers\Web\Back;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // notfication must have title,details,link
    public function show(Request $request)
    {
        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $notification = auth()->user()->notifications->findOrFail($id);

        if ($notification) {
            $notification->markAsRead();

            $link = $notification->data['link']; // استخراج الرابط من المصفوفة

            // إعادة توجيه المستخدم إلى الرابط المستخرج
            return redirect($link);
        }
    }

    public function delete(Request $request)
    {
        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        $notification = auth()->user()->notifications->find($id);

        $notification->delete();

        return redirect()->back()->with('success', __('l.Notification deleted successfully'));
    }

    public function deleteall(Request $request)
    {
        $notifications = auth()->user()->notifications;

        foreach ($notifications as $notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', __('l.All notifications deleted successfully'));
    }

    public function markall(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', __('l.All notifications marked as read successfully'));
    }

    public function sse()
    {
        if (Auth::check()) {
            $notifications = Notification::where('notifiable_id', Auth()->user()->id)
                ->where('send', 0)
                ->first();

            $unreadNotificationsCount = auth()->user()->UnreadNotifications->count();

            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');

            if ($notifications) {
                $eventData = [
                    'message' => $notifications->data,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                ];

                echo "data:" . json_encode($eventData) . "\n\n";
                $notifications->update(['send' => 1]);
            } else {
                echo "\n\n";
            }

            ob_flush();
            flush();
        }
    }
}
