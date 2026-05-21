<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Tampilkan halaman login/register ──────────────────────────
    public function showLogin()
    {
        return view('patient.login');
    }

    // ── Proses login atau register ────────────────────────────────
    public function process(Request $request)
    {
        $request->validate(['aksi' => 'required|in:register,login']);

        if ($request->aksi === 'register') {
            $request->validate([
                'username'         => 'required|string|max:255|unique:patients,username',
                'password'         => 'required|string|min:6',
                'umur'             => 'required|integer|min:1',
                'status_pekerjaan' => 'required|string|max:255',
            ]);

            $patient = Patient::create([
                'username'         => $request->username,
                'password'         => Hash::make($request->password),
                'umur'             => $request->umur,
                'status_pekerjaan' => $request->status_pekerjaan,
            ]);

            session([
                'patient_id' => $patient->id,
                'patient_name' => $patient->username,
            ]);

            return redirect()->route('patient.dashboard')
                ->with('success', 'Akun berhasil dibuat. Selamat datang di MindCheck!');
        }

        // ── Login ─────────────────────────────────────────────────
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Cek ke tabel patients (kolom username)
        $patient = Patient::where('username', $request->username)->first();

        if ($patient && $patient->password && Hash::check($request->password, $patient->password)) {
            session([
                'patient_id' => $patient->id,
                'patient_name' => $patient->username,
            ]);
            return redirect()->route('patient.dashboard');
        }

        // 2. Kalau tidak ketemu di patients, cek ke tabel admins (kolom email)
        $admin = Admin::where('email', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
            return redirect()->route('admin.dashboard');
        }

        // 3. Keduanya tidak cocok
        return back()->withErrors(['login' => 'Username/email atau password salah.'])->withInput();
    }

    // ── Logout ────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->session()->forget(['patient_id', 'patient_name']);
        return redirect()->route('landing');
    }
}