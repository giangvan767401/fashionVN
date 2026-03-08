<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        // Get the latest message for each user who has chatted with the admin
        // We find users who are not admins but have sent or received messages
        
        $adminId = Auth::id();

        // Get distinct users who have a conversation
        $userIds = Message::where('sender_id', '!=', $adminId)
            ->pluck('sender_id')
            ->merge(Message::where('receiver_id', '!=', $adminId)->pluck('receiver_id'))
            ->unique();

        $conversations = User::whereIn('id', $userIds)
            ->get()
            ->map(function ($user) use ($adminId) {
                $latestMessage = Message::where(function($q) use ($user, $adminId) {
                        $q->where('sender_id', $user->id)
                          ->where('receiver_id', $adminId);
                    })
                    ->orWhere(function($q) use ($user, $adminId) {
                        $q->where('sender_id', $adminId)
                          ->where('receiver_id', $user->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                $user->latest_message = $latestMessage;
                $user->unread_count = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->count();

                return $user;
            })
            ->sortByDesc(function ($user) {
                return $user->latest_message ? $user->latest_message->created_at : now();
            });

        return view('admin.chat.index', compact('conversations'));
    }

    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.chat.show', compact('user'));
    }

    public function fetch($userId)
    {
        $adminId = Auth::id();

        $messages = Message::where(function($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $adminId);
            })
            ->orWhere(function($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)
                  ->where('receiver_id', $userId);
            })
            ->with(['sender:id,full_name,role_id'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function store(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $message->load('sender:id,full_name,role_id');

        return response()->json($message);
    }
}
