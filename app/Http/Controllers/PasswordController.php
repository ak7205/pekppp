<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PasswordController extends Controller
{
    public function edit()
    {
        return Inertia::render('Password/Edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}