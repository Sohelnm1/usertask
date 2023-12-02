<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'usertype' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'usertype' => $request->input('usertype'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
        ]);
    }

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid email or password',
        ], 401);
    }
}

public function logout(Request $request)
{
    $request->session()->forget('custom_token');
    Auth::logout();

    if ($request->wantsJson()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }

    return redirect('/login');
}

}