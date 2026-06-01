<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;
use App\Models\Admin;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    use GuardsAdmin;

    // ── Daftar admin aktif ────────────────────────────────────────
    public function index()
    {
        $this->guardAdmin();
        $admins = Admin::latest()->get();
        return view('admin.admins.index', compact('admins'));
    }

    // ── Form tambah admin ─────────────────────────────────────────
    public function create()
    {
        $this->guardAdmin();
        return view('admin.admins.create');
    }

    // ── Simpan admin baru ─────────────────────────────────────────
    public function store(Request $request)
    {
        $this->guardAdmin();

        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => $request->status ?? 1,
        ]);

        AdminLog::record('Menambah akun admin', 'Admin', 'Admin menambahkan akun admin baru.');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    // ── Form edit admin ───────────────────────────────────────────
    public function edit($id)
    {
        $this->guardAdmin();
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    // ── Update data admin ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $this->guardAdmin();

        $admin = Admin::findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
        ]);

        $admin->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'status' => $request->status,
        ]);

        AdminLog::record('Mengubah akun admin', 'Admin', 'Admin mengubah data akun admin.');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil diperbarui.');
    }

    // ── Soft delete admin ─────────────────────────────────────────
    public function destroy($id)
    {
        $this->guardAdmin();

        if ($id == session('admin_id')) {
            return back()->with('error', 'Anda tidak dapat menghapus akun admin yang sedang aktif digunakan.');
        }

        Admin::findOrFail($id)->delete();
        AdminLog::record('Menghapus akun admin', 'Admin', 'Admin menghapus akun admin.');
        return back()->with('success', 'Admin berhasil dihapus.');
    }

    // ── Tong sampah admin (soft-deleted) ──────────────────────────
    public function trash()
    {
        $this->guardAdmin();
        $admins = Admin::onlyTrashed()->latest()->get();
        return view('admin.admins.trash', compact('admins'));
    }

    // ── Pulihkan admin ────────────────────────────────────────────
    public function restore($id)
    {
        $this->guardAdmin();
        Admin::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Admin berhasil dipulihkan.');
    }

    // ── Hapus permanen ────────────────────────────────────────────
    public function forceDelete($id)
    {
        $this->guardAdmin();
        Admin::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Admin dihapus permanen.');
    }
}
