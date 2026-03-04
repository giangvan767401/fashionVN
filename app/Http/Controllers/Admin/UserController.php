<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('role')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa chính mình.');
        }

        $name = $user->full_name;
        
        // Rename email to free it up for new registration while keeping the record
        $user->email = 'deleted_' . now()->timestamp . '_' . $user->email;
        $user->save();
        
        $user->delete();

        return redirect()->route('admin.users.index')->with('status', "Tài khoản {$name} đã được xóa vĩnh viễn.");
    }
}
