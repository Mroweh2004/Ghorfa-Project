<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.dashboard', compact('users'));
    }

    public function deleteUser(User $user)
    {
        if ($user->role !== 'admin') {
            $user->delete();
            return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully');
        }
        return redirect()->route('admin.dashboard')->with('error', 'Cannot delete admin user');
    }
} 