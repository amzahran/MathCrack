<?php

namespace App\Http\Controllers\Web\Back\Users\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\User;
use App\Jobs\TicketsJob;
use App\Models\TicketMessage;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::where('user_id' , auth()->user()->id)->orderByDesc('id')->get();

        return view(theme('back.users.tickets.tickets'), ['tickets' => $tickets]);
    }

    public function show(Request $request)
    {
        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        $ticket = Ticket::find($id);

        if($ticket->user_id != auth()->user()->id){
            return view('themes/default/back.permission-denied');
        }

        return view(theme('back.users.tickets.tickets-show'), ['ticket' => $ticket]);
    }

    public function store(Request $request)
    {
        $ticket = new Ticket();
        $ticket->subject=$request->subject;
        $ticket->description=$request->description;
        $ticket->support_type=$request->support_type;
        $ticket->user_id=auth()->user()->id;
        $ticket->save();

        $users = User::permission('show tickets')->get();
        $type = 'create';
        TicketsJob::dispatch($users, $ticket, $type);

        return redirect()->route('dashboard.users.tickets-show', ['id' => encrypt($ticket->id)])->with('success', __('l.ticket created successfully.'));
    }

    public function reply(Request $request)
    {
        $ticket = Ticket::findOrFail($request->input('ticket_id'));

        if($ticket->user_id != auth()->user()->id){
            return view('themes/default/back.permission-denied');
        }

        $comment = new TicketMessage();
        $comment->ticket_id = $ticket->id;
        $comment->user_id = auth()->user()->id;

        if ($request->has('message') && !empty($request->input('message'))) {
            $comment->content = $request->input('message');
        } else if ($request->has('audio_message') && !empty($request->input('audio_message'))) {
            $comment->content = $request->input('audio_message');
        } else {
            return redirect()->back()->with('error', __('l.Message cannot be empty'));
        }

        if ($request->hasFile('attachment')) {
            $filename = time() . '.' . $request->file('attachment')->getClientOriginalExtension();
            $path = 'files/tickets/attachments/' . $filename;

            move_uploaded_file($request->file('attachment')->getPathname(), $path);

            // استخدام المسار الكامل للملف لحفظه في قاعدة البيانات
            $comment->attachment = $path;
        }

        $comment->save();

        $ticket->status = 'in_progress';
        $ticket->save();

        $users = User::permission('show tickets')->get();
        $type = 'user reply';
        TicketsJob::dispatch($users, $ticket, $type);

        return redirect()->back()->with(['done' => __('l.Replied successfully.'), 'id' => encrypt($ticket->id)]);
    }


    public function getNewMessages(Request $request)
    {
        $ticketId = decrypt($request->ticket_id);
        $lastMessageId = $request->last_message_id;

        $ticket = Ticket::findOrFail($ticketId);

        if($ticket->user_id != auth()->user()->id){
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $newMessages = TicketMessage::where('ticket_id', $ticketId)
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->get();

        $messages = [];
        foreach ($newMessages as $message) {
            $messages[] = [
                'id' => $message->id,
                'content' => $message->content,
                'attachment' => $message->attachment,
                'created_at' => $message->created_at->diffForHumans(),
                'is_user' => $message->user_id == $message->ticket->user_id,
                'user' => [
                    'photo' => $message->user->photo,
                    'name' => $message->user->firstname . ' ' . $message->user->lastname
                ]
            ];
        }

        return response()->json($messages);
    }
}