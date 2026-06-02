<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Tampilkan form login ──────────────────────────────────────
    public function showLogin()
    {
        if (session('admin_id')) return redirect()->route('admin.dashboard');
        return view('patient.login');
    }

    // ── Proses login ──────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::withTrashed()->where('email', $request->email)->first();
        if ($admin) {
            if ($admin->trashed()) {
                return back()->withErrors(['email' => 'Akun admin Anda sedang dinonaktifkan/berada di tempat sampah.'])->withInput();
            }
            if (Hash::check($request->password, $admin->password)) {
                session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
                AdminLog::record('Login admin', 'Login', 'Admin berhasil masuk ke sistem.');
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Email atau kata sandi salah.'])->withInput();
    }

    // ── Logout ────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id', 'admin_name']);
        return redirect()->route('patient.login');
    }
}