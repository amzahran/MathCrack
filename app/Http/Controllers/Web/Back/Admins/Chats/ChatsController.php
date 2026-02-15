<?php

namespace App\Http\Controllers\Web\Back\Admins\Chats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Jobs\ChatMessageJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Notifications\Chats\AddedToGroupChat;
use App\Jobs\AddUserToGroupChatJob;

class ChatsController extends Controller
{
    /**
     * عرض قائمة المحادثات للمستخدم الحالي
     */
    public function index(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return view('themes/default/back.permission-denied');
        }

        $user = auth()->user();

        // الحصول على المحادثات مع آخر رسالة لكل محادثة
        $chats = Chat::whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['participants.user', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('chat_messages')
                    ->whereColumn('chat_id', 'chats.id')
                    ->latest()
                    ->limit(1);
            })
            ->get();

        // إضافة عدد الرسائل غير المقروءة لكل محادثة
        foreach ($chats as $chat) {
            // الحصول على مشاركة المستخدم الحالي
            $participant = $chat->participants->where('user_id', $user->id)->first();

            if ($participant) {
                // حساب عدد الرسائل غير المقروءة
                $chat->unread_count = $participant->unreadMessagesCount();
            } else {
                $chat->unread_count = 0;
            }
        }

        // حساب مجموع الرسائل غير المقروءة في الشاتات والجروبات
        $totalDirectUnreplied = 0;
        $totalGroupUnreplied = 0;

        foreach ($chats as $chat) {
            if ($chat->is_group) {
                $totalGroupUnreplied += $chat->unread_count;
            } else {
                $totalDirectUnreplied += $chat->unread_count;
            }
        }

        // الحصول على قائمة المستخدمين لإنشاء محادثات جديدة
        $users = User::whereHas('roles')
            ->whereNotIn('id', [auth()->user()->id, 1])
            ->get();

        return view('themes/default/back.admins.chats.chats', compact('chats', 'users', 'totalDirectUnreplied', 'totalGroupUnreplied'));
    }

    /**
     * عرض محادثة معينة
     */
    public function show(Request $request)
    {
        if (!Gate::allows('show chats')) {
            if ($request->ajax()) {
                return response()->json(['error' => __('l.Permission denied')], 403);
            }
            return view('themes/default/back.permission-denied');
        }

        try {
            if (!$request->has('id')) {
                throw new \Exception(__('l.Chat ID is required'));
            }

            $encryptedId = $request->id;
            try {
                $id = decrypt($encryptedId);
            } catch (\Exception $e) {
                Log::error('Chat decryption error: ' . $e->getMessage());
                throw new \Exception(__('l.Invalid chat ID'));
            }

            $chat = Chat::with([
                'participants.user',
                'messages' => function ($query) {
                    $query->with('user')->orderBy('created_at', 'asc');
                }
            ])->find($id);

            if (!$chat) {
                throw new \Exception(__('l.Chat not found'));
            }

            // التحقق من أن المستخدم الحالي هو مشارك في المحادثة
            if (!$chat->hasParticipant(auth()->id())) {
                if ($request->ajax()) {
                    return response()->json(['error' => __('l.You are not a participant in this chat')], 403);
                }
                return view('themes/default/back.permission-denied');
            }

            // تحديث وقت آخر قراءة
            $participant = ChatParticipant::where('chat_id', $chat->id)
                ->where('user_id', auth()->id())
                ->first();

            if ($participant) {
                $participant->markAsRead();
            }

            // الحصول على باقي المستخدمين لإضافتهم إلى مجموعة
            $usersToAdd = [];
            if ($chat->is_group) {
                $existingUserIds = $chat->participants->pluck('user_id')->toArray();
                $usersToAdd = User::whereNotIn('id', array_merge($existingUserIds, [1]))
                    ->whereHas('roles')
                    ->get();
            }

            // الحصول على المشارك الآخر في المحادثة الثنائية
            $otherParticipant = null;
            if (!$chat->is_group) {
                $otherParticipant = $chat->participants->where('user_id', '!=', auth()->id())->first();
            }

            if ($request->ajax()) {
                $messages = [];
                foreach ($chat->messages as $message) {
                    // التحقق من حالة قراءة الرسالة
                    $isRead = false;
                    if ($message->user_id == auth()->id()) {
                        // إذا كان المستخدم الحالي هو من أرسل الرسالة، نتحقق من وقت آخر قراءة لدى المشاركين الآخرين
                        foreach ($chat->participants as $p) {
                            if ($p->user_id != auth()->id() && $p->last_read_at && $p->last_read_at >= $message->created_at) {
                                $isRead = true;
                                break;
                            }
                        }
                    }

                    $messages[] = [
                        'id' => $message->id,
                        'content' => $message->content,
                        'attachment' => $message->attachment,
                        'attachment_type' => $message->attachment_type,
                        'created_at' => $message->created_at->diffForHumans(),
                        'created_at_timestamp' => $message->created_at->timestamp * 1000,
                        'created_at_formatted' => $message->created_at->format('Y-m-d H:i:s'),
                        'is_self' => $message->user_id == auth()->id(),
                        'is_read' => $isRead,
                        'user' => [
                            'photo' => $message->user->photo,
                            'name' => $message->user->firstname . ' ' . $message->user->lastname,
                            'is_online' => $message->user->isOnline()
                        ]
                    ];
                }

                return response()->json([
                    'success' => true,
                    'chat' => [
                        'id' => $chat->id,
                        'encrypted_id' => encrypt($chat->id),
                        'name' => $chat->is_group ? $chat->name : ($otherParticipant ? $otherParticipant->user->firstname . ' ' . $otherParticipant->user->lastname : __('l.Unknown User')),
                        'is_group' => $chat->is_group,
                        'participants' => $chat->participants->map(function($participant) {
                            return [
                                'id' => $participant->user->id,
                                'name' => $participant->user->firstname . ' ' . $participant->user->lastname,
                                'email' => $participant->user->email,
                                'photo' => $participant->user->photo,
                                'is_admin' => $participant->is_admin,
                                'is_online' => $participant->user->isOnline()
                            ];
                        }),
                        'users_to_add' => collect($usersToAdd)->map(function($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->firstname . ' ' . $user->lastname,
                                'email' => $user->email
                            ];
                        })
                    ],
                    'messages' => $messages
                ]);
            }

            return redirect()->route('dashboard.admins.chats', ['id' => $encryptedId]);
        } catch (\Exception $e) {
            Log::error('Chat loading error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return redirect()->route('dashboard.admins.chats')->with('error', __('l.Error loading chat: ') . $e->getMessage());
        }
    }

    /**
     * إنشاء محادثة جديدة
     */
    public function store(Request $request)
    {
        if (!Gate::allows('add chats')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'is_group' => 'required|boolean',
            'name' => 'required_if:is_group,1|max:255',
            'description' => 'nullable|max:1000',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
        ]);

        $user = auth()->user();

        // التأكد من أن المستخدم يملك صلاحية إنشاء محادثات
        if ($request->is_group && !Gate::allows('add chats')) {
            return view('themes/default/back.permission-denied');
        }

        // لو كانت المحادثة ثنائية (غير مجموعة)، نتحقق إذا كانت موجودة مسبقًا
        if (!$request->is_group && count($request->participants) == 1) {
            $otherUserId = $request->participants[0];

            // البحث عن محادثة ثنائية سابقة بين المستخدمين
            $existingChat = Chat::where('is_group', false)
                ->whereHas('participants', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereHas('participants', function($query) use ($otherUserId) {
                    $query->where('user_id', $otherUserId);
                })
                ->first();

            // إذا وجدت محادثة سابقة، نعيد توجيه المستخدم إليها
            if ($existingChat) {
                return redirect()->route('dashboard.admins.chats', ['id' => encrypt($existingChat->id)])
                    ->with('info', __('l.Chat already exists'));
            }
        }

        DB::beginTransaction();

        try {
            // إنشاء المحادثة
            $chat = new Chat();
            $chat->is_group = $request->is_group;
            $chat->name = $request->is_group ? $request->name : null;
            $chat->description = $request->description;
            $chat->created_by = $user->id;
            $chat->save();

            // إضافة المشاركين
            $participants = $request->participants;

            // إضافة المستخدم الحالي كمدير للمحادثة
            $chat->addParticipants([$user->id], true);

            // إضافة المشاركين الآخرين
            $chat->addParticipants($participants);

            try {
                // إرسال إشعارات للمستخدمين المضافين إلى مجموعة
                if ($request->is_group) {
                    foreach ($participants as $participantId) {
                        AddUserToGroupChatJob::dispatch($chat, User::find($participantId), $user);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Chat notification error: ' . $e->getMessage());
            }

            DB::commit();

            // إنشاء رسالة افتتاحية للمحادثة الجماعية
            if ($request->is_group) {
                $message = new ChatMessage();
                $message->chat_id = $chat->id;
                $message->user_id = $user->id;
                $message->content = __('l.Group created successfully');
                $message->save();

                // إرسال إشعار بالرسالة الأولى
                ChatMessageJob::dispatch($chat, $message, $user);
            }

            return redirect()->route('dashboard.admins.chats', ['id' => encrypt($chat->id)])->with('success', __('l.Chat created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('l.Error creating chat: ') . $e->getMessage());
        }
    }

    /**
     * إرسال رسالة في محادثة
     */
    public function sendMessage(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'chat_id' => 'required',
            'content' => 'nullable',
            'attachment' => 'nullable|file|max:10240', // 10MB كحد أقصى
            'audio_message' => 'nullable|string' // للرسائل الصوتية
        ]);

        if (empty($request->content) && !$request->hasFile('attachment') && empty($request->audio_message)) {
            return redirect()->back()->with('error', __('l.Message cannot be empty'));
        }

        $chatId = decrypt($request->chat_id);
        $chat = Chat::findOrFail($chatId);

        // التحقق من أن المستخدم مشارك في المحادثة
        if (!$chat->hasParticipant(auth()->id())) {
            return view('themes/default/back.permission-denied');
        }

        $message = new ChatMessage();
        $message->chat_id = $chat->id;
        $message->user_id = auth()->id();

        // معالجة الرسالة الصوتية
        if ($request->audio_message) {
            $message->content = $request->audio_message;
        } else {
            $message->content = $request->content ?? '';
        }

        // معالجة المرفقات
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = 'files/chats/attachments/' . $fileName;

            // تحديد نوع الملف
            $extension = strtolower($file->getClientOriginalExtension());
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
            $audioExtensions = ['mp3', 'wav', 'ogg'];
            $videoExtensions = ['mp4', 'avi', 'mov', 'wmv'];

            if (in_array($extension, $imageExtensions)) {
                $message->attachment_type = 'image';
            } elseif (in_array($extension, $documentExtensions)) {
                $message->attachment_type = 'document';
            } elseif (in_array($extension, $audioExtensions)) {
                $message->attachment_type = 'audio';
            } elseif (in_array($extension, $videoExtensions)) {
                $message->attachment_type = 'video';
            }

            move_uploaded_file($file->getPathname(), $path);
            $message->attachment = $path;
        }

        $message->save();

        // إرسال إشعار إلى جميع المشاركين في المحادثة
        ChatMessageJob::dispatch($chat, $message, auth()->user());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'attachment' => $message->attachment,
                    'attachment_type' => $message->attachment_type,
                    'created_at' => $message->created_at->diffForHumans(),
                    'created_at_timestamp' => $message->created_at->timestamp * 1000,
                    'created_at_formatted' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_self' => true,
                    'user' => [
                        'photo' => auth()->user()->photo,
                        'name' => auth()->user()->firstname . ' ' . auth()->user()->lastname
                    ]
                ]
            ]);
        }

        return redirect()->back()->with('success', __('l.Message sent successfully'));
    }

    /**
     * إضافة مستخدمين إلى مجموعة
     */
    public function addUsersToGroup(Request $request)
    {
        if (!Gate::allows('edit chats')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'chat_id' => 'required',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ]);

        $chatId = decrypt($request->chat_id);
        $chat = Chat::findOrFail($chatId);

        // التحقق من أن المستخدم مدير في المجموعة
        $participant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->first();

        if (!$participant) {
            return view('themes/default/back.permission-denied');
        }

        // إضافة المستخدمين الجدد
        $chat->addParticipants($request->users);

        try {
            // إرسال إشعارات للمستخدمين الجدد من خلال وظيفة مُجدولة
            foreach ($request->users as $userId) {
                AddUserToGroupChatJob::dispatch($chat, User::find($userId), auth()->user());
            }
        } catch (\Exception $e) {
            Log::error('Chat notification error: ' . $e->getMessage());
        }

        // إنشاء رسالة إعلامية في المجموعة
        $newUserNames = User::whereIn('id', $request->users)->pluck('firstname')->implode(', ');

        $message = new ChatMessage();
        $message->chat_id = $chat->id;
        $message->user_id = auth()->id();
        $message->content = __('l.Added :users to the group', ['users' => $newUserNames]);
        $message->save();

        // إرسال إشعار بالرسالة الجديدة
        ChatMessageJob::dispatch($chat, $message, auth()->user());

        return redirect()->back()->with('success', __('l.Users added successfully'));
    }

    /**
     * مغادرة مجموعة
     */
    public function leaveGroup(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return view('themes/default/back.permission-denied');
        }

        $chatId = decrypt($request->chat_id);
        $chat = Chat::findOrFail($chatId);

        // التحقق من أن المحادثة هي مجموعة
        if (!$chat->is_group) {
            return redirect()->back()->with('error', __('l.This is not a group chat'));
        }

        // حذف المستخدم من المشاركين
        $participant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($participant) {
            // إذا كان المستخدم هو المدير الوحيد، قم بتعيين مدير جديد
            if ($participant->is_admin) {
                $adminsCount = ChatParticipant::where('chat_id', $chat->id)
                    ->where('is_admin', true)
                    ->count();

                if ($adminsCount <= 1) {
                    // تعيين مشارك آخر كمدير
                    $newAdmin = ChatParticipant::where('chat_id', $chat->id)
                        ->where('user_id', '!=', auth()->id())
                        ->first();

                    if ($newAdmin) {
                        $newAdmin->is_admin = true;
                        $newAdmin->save();

                        // إنشاء رسالة إعلامية
                        $message = new ChatMessage();
                        $message->chat_id = $chat->id;
                        $message->user_id = auth()->id();
                        $message->content = __('l.New admin assigned') . " " . $newAdmin->user->firstname . " " . $newAdmin->user->lastname . " " . __('l.as admin');
                        $message->save();
                    }
                }
            }

            // إنشاء رسالة إعلامية بالمغادرة
            $message = new ChatMessage();
            $message->chat_id = $chat->id;
            $message->user_id = auth()->id();
            $message->content = __('l.Left the group') . " " . auth()->user()->firstname . " " . auth()->user()->lastname;
            $message->save();

            // حذف المستخدم من المجموعة
            $participant->delete();
        }

        return redirect()->route('dashboard.admins.chats')->with('success', __('l.You have left the group'));
    }

    /**
     * الحصول على الرسائل الجديدة في محادثة
     */
    public function getNewMessages(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return view('themes/default/back.permission-denied');
        }

        $chatId = decrypt($request->chat_id);
        $lastMessageId = $request->last_message_id;

        $chat = Chat::with('participants')->findOrFail($chatId);

        // التحقق من أن المستخدم مشارك في المحادثة
        if (!$chat->hasParticipant(auth()->id())) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        // تحديث وقت آخر قراءة
        $participant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($participant) {
            $participant->markAsRead();
        }

        // الحصول على الرسائل الجديدة
        $newMessages = ChatMessage::where('chat_id', $chatId)
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->get();

        $messages = [];
        foreach ($newMessages as $message) {
            // التحقق من حالة قراءة الرسالة
            $isRead = false;
            if ($message->user_id == auth()->id()) {
                // إذا كان المستخدم الحالي هو من أرسل الرسالة، نتحقق من وقت آخر قراءة لدى المشاركين الآخرين
                foreach ($chat->participants as $p) {
                    if ($p->user_id != auth()->id() && $p->last_read_at && $p->last_read_at >= $message->created_at) {
                        $isRead = true;
                        break;
                    }
                }
            }

            $messages[] = [
                'id' => $message->id,
                'content' => $message->content,
                'attachment' => $message->attachment,
                'attachment_type' => $message->attachment_type,
                'created_at' => $message->created_at->diffForHumans(),
                'created_at_timestamp' => $message->created_at->timestamp * 1000,
                'created_at_formatted' => $message->created_at->format('Y-m-d H:i:s'),
                'is_self' => $message->user_id == auth()->id(),
                'is_read' => $isRead,
                'user' => [
                    'photo' => $message->user->photo,
                    'name' => $message->user->firstname . ' ' . $message->user->lastname
                ]
            ];
        }

        return response()->json($messages);
    }

    /**
     * البحث عن مستخدمين لإنشاء محادثة جديدة
     */
    public function searchUsers(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return response()->json(['error' => __('l.Permission denied')], 403);
        }

        $query = trim($request->get('query'));

        if (empty($query)) {
            return response()->json([]);
        }

        $users = User::whereHas('roles')
            ->whereNotIn('id', [auth()->user()->id, 1])
            ->where(function ($q) use ($query) {
                $q->where(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', "%{$query}%")
                  ->orWhere('firstname', 'like', "%{$query}%")
                  ->orWhere('lastname', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->select('id', 'firstname', 'lastname', 'email', 'photo')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                    'photo' => $user->photo
                ];
            });

        return response()->json($users);
    }

    /**
     * حذف مستخدم من محادثة جماعية
     */
    public function removeUserFromGroup(Request $request)
    {
        if (!Gate::allows('edit chats')) {
            return response()->json(['error' => __('l.Permission denied')], 403);
        }

        $request->validate([
            'chat_id' => 'required',
            'user_id' => 'required|exists:users,id',
        ]);

        // فك تشفير معرف المحادثة
        try {
            $chatId = decrypt($request->chat_id);
        } catch (\Exception $e) {
            return response()->json(['error' => __('l.Invalid chat ID')], 400);
        }

        $chat = Chat::findOrFail($chatId);

        // التحقق من أن المحادثة هي مجموعة
        if (!$chat->is_group) {
            return response()->json(['error' => __('l.This is not a group chat')], 400);
        }

        // التحقق من أن المستخدم الحالي هو مدير في المجموعة
        $currentUserParticipant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->first();

        if (!$currentUserParticipant) {
            return response()->json(['error' => __('l.You are not an admin of this group')], 403);
        }

        $userToRemove = (int)$request->user_id;

        // التحقق من أن المستخدم ليس المستخدم الحالي
        if ($userToRemove === auth()->id()) {
            return response()->json(['error' => __('l.You cannot remove yourself from the group')], 400);
        }

        // التحقق من أن المستخدم مشارك في المحادثة
        $participant = ChatParticipant::where('chat_id', $chat->id)
            ->where('user_id', $userToRemove)
            ->first();

        if (!$participant) {
            return response()->json(['error' => __('l.User is not a participant in this group')], 400);
        }

        // الحصول على اسم المستخدم المحذوف لاستخدامه في الرسالة الإعلامية
        $removedUser = User::find($userToRemove);

        if (!$removedUser) {
            return response()->json(['error' => __('l.User not found')], 400);
        }

        // حذف المستخدم من المجموعة
        $participant->delete();

        // إضافة رسالة إعلامية في المحادثة
        $message = new ChatMessage();
        $message->chat_id = $chat->id;
        $message->user_id = auth()->id();
        $message->content = __('l.:admin removed :user from the group', [
            'admin' => auth()->user()->firstname . ' ' . auth()->user()->lastname,
            'user' => $removedUser->firstname . ' ' . $removedUser->lastname
        ]);
        $message->save();

        return response()->json([
            'success' => true,
            'message' => __('l.User has been removed from the group')
        ]);
    }

    /**
     * التحقق من حالة قراءة الرسائل
     */
    public function checkReadStatus(Request $request)
    {
        if (!Gate::allows('show chats')) {
            return response()->json(['error' => __('l.Permission denied')], 403);
        }

        try {
            $chatId = decrypt($request->chat_id);
            $chat = Chat::with('participants')->findOrFail($chatId);

            // التحقق من أن المستخدم مشارك في المحادثة
            if (!$chat->hasParticipant(auth()->id())) {
                return response()->json(['error' => __('l.You are not a participant in this chat')], 403);
            }

            // الحصول على رسائل المستخدم الحالي في هذه المحادثة
            $myMessages = ChatMessage::where('chat_id', $chatId)
                ->where('user_id', auth()->id())
                ->pluck('id', 'created_at');

            // قائمة معرفات الرسائل المقروءة
            $readMessageIds = [];

            // التحقق من كل رسالة إذا تمت قراءتها من قبل جميع المشاركين الآخرين
            foreach ($myMessages as $createdAt => $messageId) {
                $isRead = true;
                foreach ($chat->participants as $participant) {
                    // تخطي المستخدم الحالي
                    if ($participant->user_id == auth()->id()) continue;

                    // التحقق من وقت آخر قراءة
                    if (!$participant->last_read_at || $participant->last_read_at < $createdAt) {
                        $isRead = false;
                        break;
                    }
                }

                if ($isRead) {
                    $readMessageIds[] = $messageId;
                }
            }

            return response()->json([
                'success' => true,
                'read_message_ids' => $readMessageIds
            ]);
        } catch (\Exception $e) {
            Log::error('Chat read status error: ' . $e->getMessage());
            return response()->json(['error' => __('l.Error checking read status: ') . $e->getMessage()], 500);
        }
    }
}