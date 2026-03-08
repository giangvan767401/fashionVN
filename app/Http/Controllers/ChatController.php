<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function fetch()
    {
        // Admin has role_id = 1. We assume the first found admin is the main admin for simplicity.
        $admin = User::where('role_id', 1)->first();
        if (!$admin) {
            return response()->json([]);
        }

        $userId = Auth::id();
        $adminId = $admin->id;

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

        // Mark as read optionally
        Message::where('sender_id', $adminId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $admin = User::where('role_id', 1)->first();
        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $admin->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Load sender info for immediate display
        $message->load('sender:id,full_name,role_id');

        return response()->json($message);
    }
}
