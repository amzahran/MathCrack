<?php

namespace App\Http\Controllers\Web\Back\Admins\Tickets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $users = User::select('id', 'email', 'firstname', 'lastname')
            ->orderByDesc('id')
            ->whereNotIn('id', [1, 2])
            ->get();

        if ($request->ajax()) {
            $query = Ticket::with('user')->select('tickets.*');

            if (!isset($request->inactive)) {
                $query->where('status', '!=', 'closed');
            } else {
                $query->where('status', 'closed');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user', function($ticket) {
                    return [
                        'photo' => $ticket->user->photo,
                        'name' => $ticket->user->firstname . ' ' . $ticket->user->lastname. ' ('.$ticket->user->email.')',
                        'encrypted_id' => encrypt($ticket->user->id),
                        'sort_name' => $ticket->user->firstname . ' ' . $ticket->user->lastname
                    ];
                })
                ->filterColumn('subject', function($query, $keyword) {
                    $query->where('subject', 'like', "%{$keyword}%");
                })
                ->filterColumn('support_type', function($query, $keyword) {
                    $query->where('support_type', 'like', "%{$keyword}%");
                })
                ->filterColumn('status', function($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                ->filterColumn('user', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('firstname', 'like', "%{$keyword}%")
                          ->orWhere('lastname', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->orderColumn('user', function ($query, $order) {
                    $query->orderBy(function($q) {
                        $q->select('firstname')
                          ->from('users')
                          ->whereColumn('users.id', 'tickets.user_id')
                          ->limit(1);
                    }, $order);
                })
                ->addColumn('action', function($ticket) {
                    $actions = '';
                    if (Gate::allows('show tickets')) {
                        $actions .= '<a href="' . route('dashboard.admins.tickets-show') . '?id=' . encrypt($ticket->id) . '"
                            class="btn btn-sm btn-info waves-effect" data-bs-toggle="tooltip" title="show">
                            <i class="fa fa-eye"></i>
                        </a> ';
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('themes/default/back/admins/tickets/tickets-list', compact('users'));
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $ticket = new Ticket();
        $ticket->subject=$request->subject;
        $ticket->description=$request->description;
        $ticket->support_type=$request->support_type;
        $ticket->user_id=$request->user_id;
        $ticket->save();

        $users = $ticket->user;
        $type = 'admin_create';
        \App\Jobs\TicketsJob::dispatch($users, $ticket, $type);

        return redirect()->back()->with('success', __('l.Ticket created successfully'));
    }

    public function show(Request $request)
    {
        if (!Gate::allows('show tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        $ticket = Ticket::findOrFail($id);

        return view('themes/default/back.admins.tickets.tickets-show', compact('ticket'));
    }

    public function reply(Request $request)
    {
        if (!Gate::allows('add tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $ticket = Ticket::findOrFail($request->input('ticket_id'));

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

            $comment->attachment = $path;
        }

        $comment->save();

        $ticket->status = 'answered';
        $ticket->save();

        $users = $ticket->user;
        $type = 'admin reply';
        \App\Jobs\TicketsJob::dispatch($users, $ticket, $type);

        return redirect()->back()->with('success', __('l.Replied successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $ticket = Ticket::find($id);

        if (!$ticket) {
            return redirect()->back()->with('error', __('l.Ticket does not exist'));
        }

        $ticketMessages = $ticket->ticketMessages;

        foreach ($ticketMessages as $comment) {
            if (!empty($comment->attachment)) {
                $filePath = public_path($comment->attachment);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $ticket->ticketMessages()->delete();

        $ticket->delete();

        return redirect()->route('dashboard.admins.tickets')->with('success', __('l.Ticket deleted successfully'));
    }


    public function close(Request $request)
    {
        if (!Gate::allows('edit tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->input('id');
        $id = decrypt($encryptedId);

        $ticket = Ticket::find($id);

        $ticket->status = 'closed';
        $ticket->save();

        return redirect()->back()->with('success', __('l.Ticket closed successfully'));
    }

    public function active(Request $request)
    {
        if (!Gate::allows('edit tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->input('id');
        $id = decrypt($encryptedId);

        $ticket = Ticket::find($id);

        $ticket->status = 'answered';
        $ticket->save();

        return redirect()->back()->with('success', __('l.Ticket active successfully'));
    }

    public function deleteAll(Request $request)
    {
        if (!Gate::allows('delete tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $tickets = Ticket::where('status', 'closed')->get();

        if ($tickets->isEmpty()) {
            return redirect()->back()->with('error', __('l.No closed tickets found to delete'));
        }

        foreach ($tickets as $ticket) {
            $ticketMessages = $ticket->ticketMessages;

            foreach ($ticketMessages as $comment) {
                if (!empty($comment->attachment)) {
                    $filePath = public_path($comment->attachment);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            $ticket->ticketMessages()->delete();
        }

        Ticket::where('status', 'closed')->delete();

        return redirect()->back()->with('success', __('l.All closed tickets and their attachments deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete tickets')) {
            return view('themes/default/back.permission-denied');
        }

        $ids = explode(',', $request->ids);

        $tickets = Ticket::whereIn('id', $ids)->get();

        foreach ($tickets as $ticket) {
            $ticketMessages = $ticket->ticketMessages;

            foreach ($ticketMessages as $message) {
                if (!empty($message->attachment)) {
                    $filePath = public_path($message->attachment);
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            $ticket->ticketMessages()->delete();
            $ticket->delete();
        }

        return redirect()->back()->with('success', __('l.Selected tickets have been deleted successfully'));
    }

    public function getNewMessages(Request $request)
    {
        if (!Gate::allows('show tickets')) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $ticketId = decrypt($request->ticket_id);
        $lastMessageId = $request->last_message_id;

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