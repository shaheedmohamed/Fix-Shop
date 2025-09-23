<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function conversations(Request $request)
    {
        $user = $request->user();
        $convs = Conversation::query()
            ->select('conversations.*')
            ->join('conversation_participants as cp', 'cp.conversation_id', '=', 'conversations.id')
            ->where('cp.user_id', $user->id)
            ->with(['participants:id,name', 'messages' => function($q){ $q->latest()->limit(1); }])
            ->orderByDesc(
                DB::raw('(select max(created_at) from user_messages where user_messages.conversation_id = conversations.id)')
            )
            ->limit(100)
            ->get()
            ->map(function($c) use ($user){
                $last = $c->messages->first();
                $unread = DB::table('user_messages as m')
                    ->where('m.conversation_id', $c->id)
                    ->where('m.sender_id', '!=', $user->id)
                    ->where(function($q) use ($c, $user){
                        $lr = DB::table('conversation_participants')
                            ->where('conversation_id', $c->id)
                            ->where('user_id', $user->id)
                            ->value('last_read_at');
                        if ($lr) $q->where('m.created_at', '>', $lr);
                    })
                    ->count();
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'participants' => $c->participants->map(fn($p)=>['id'=>$p->id,'name'=>$p->name]),
                    'last_message' => $last ? [
                        'id' => $last->id,
                        'sender_id' => $last->sender_id,
                        'content' => $last->content,
                        'created_at' => $last->created_at,
                    ] : null,
                    'unread' => $unread,
                ];
            });
        return response()->json($convs);
    }

    public function start(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'message' => ['nullable','string'],
        ]);
        $otherId = (int) $data['user_id'];
        if ($otherId === $user->id) return response()->json(['message' => 'Cannot start a conversation with yourself'], 422);

        // find existing two-participant conversation (both users in same conversation)
        $convId = DB::table('conversation_participants')
            ->select('conversation_id')
            ->whereIn('user_id', [$user->id, $otherId])
            ->groupBy('conversation_id')
            ->havingRaw('COUNT(DISTINCT user_id) = 2')
            ->orderByDesc('conversation_id')
            ->value('conversation_id');

        if (!$convId) {
            $conv = Conversation::create([ 'title' => null ]);
            $conv->participants()->attach([$user->id, $otherId]);
        } else {
            $conv = Conversation::findOrFail($convId);
        }

        // optional first message
        if (!empty($data['message'])) {
            UserMessage::create([
                'conversation_id' => $conv->id,
                'sender_id' => $user->id,
                'content' => $data['message'],
            ]);
        }

        return response()->json(['id' => $conv->id]);
    }

    public function startAdmin(Request $request)
    {
        $user = $request->user();
        // find an admin user (first by id); exclude self if user is admin
        $admin = User::query()
            ->where('role', 'admin')
            ->when($user->role === 'admin', function($q) use ($user){ $q->where('id', '!=', $user->id); })
            ->orderBy('id')
            ->first();
        if (!$admin) return response()->json(['message' => 'No admin available'], 404);

        // reuse start logic
        $request->merge(['user_id' => $admin->id]);
        return $this->start($request);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $conv = Conversation::findOrFail($id);
        $isParticipant = DB::table('conversation_participants')
            ->where('conversation_id', $conv->id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isParticipant) return response()->json(['message' => 'Forbidden'], 403);

        $messages = UserMessage::query()
            ->where('conversation_id', $conv->id)
            ->orderBy('created_at')
            ->limit(500)
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'role' => $m->sender_id === $user->id ? 'user' : 'other',
                'sender_id' => $m->sender_id,
                'content' => $m->content,
                'created_at' => $m->created_at,
            ]);

        return response()->json([
            'id' => $conv->id,
            'title' => $conv->title,
            'messages' => $messages,
        ]);
    }

    public function send(Request $request, $id)
    {
        $user = $request->user();
        $data = $request->validate(['content' => ['required','string']]);
        $conv = Conversation::findOrFail($id);
        $isParticipant = DB::table('conversation_participants')
            ->where('conversation_id', $conv->id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isParticipant) return response()->json(['message' => 'Forbidden'], 403);

        $msg = UserMessage::create([
            'conversation_id' => $conv->id,
            'sender_id' => $user->id,
            'content' => trim($data['content']),
        ]);

        return response()->json([
            'id' => $msg->id,
            'sender_id' => $msg->sender_id,
            'content' => $msg->content,
            'created_at' => $msg->created_at,
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $user = $request->user();
        $conv = Conversation::findOrFail($id);
        $isParticipant = DB::table('conversation_participants')
            ->where('conversation_id', $conv->id)
            ->where('user_id', $user->id)
            ->exists();
        if (!$isParticipant) return response()->json(['message' => 'Forbidden'], 403);

        DB::table('conversation_participants')
            ->where('conversation_id', $conv->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = DB::table('conversation_participants as cp')
            ->join('user_messages as m', 'm.conversation_id', '=', 'cp.conversation_id')
            ->where('cp.user_id', $user->id)
            ->where('m.sender_id', '!=', $user->id)
            ->where(function($q) use ($user){
                $q->whereNull('cp.last_read_at')
                  ->orWhere('m.created_at', '>', DB::raw('cp.last_read_at'));
            })
            ->count();
        return response()->json(['count' => $count]);
    }
}
