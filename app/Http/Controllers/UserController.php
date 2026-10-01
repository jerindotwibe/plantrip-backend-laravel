<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all()->map(function ($user) {
            $role = $user->email === 'admin@plantriponline.com' ? 'Admin' : 'User';
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
                'status' => 'Active',
                'date' => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A'
            ];
        });

        return response()->json(['users' => $users]);
    }
}
