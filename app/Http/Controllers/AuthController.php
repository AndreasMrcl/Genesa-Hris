<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function signin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // User Login)
        $user = User::where('email', $request->email)->first();

        if ($user) {
            
                if (!Auth::guard('web')->attempt($credentials)) {
                return back()->withErrors(['email' => 'Email atau password salah']);
            }

            $loginUser = Auth::guard('web')->user();

            $token = $loginUser->createToken('auth_token')->plainTextToken;

            // Redirect berdasarkan level
            return redirect()->route('dashboard')
                    ->with('auth_token', $token)
                    ->with('toast_success', 'Login Success!');

            }

        // Employee Login
        $employee = Employee::where('email', $request->email)->first();

        if ($employee) {

            if (!Auth::guard('employee')->attempt($credentials)) {
                return back()->withErrors(['email' => 'Email atau password salah']);
            }

            $loginEmp = Auth::guard('employee')->user();

            $token = $loginEmp->createToken('auth_token')->plainTextToken;

            // Redirect berdasarkan level employee
            return redirect()->route('ess-home')
                    ->with('auth_token', $token)
                    ->with('toast_success', 'Login Success!');
            }

        return back()->withErrors(['email' => 'Akun tidak ditemukan']);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete(); // Delete all tokens
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')->with('toast_success', 'Logged Out Successful!');
    }
}
