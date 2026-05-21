<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $screeningResumeMinutes = Setting::getValue('screening_resume_minutes', 30);

        $adminName = session('admin_name') ?? session('admin_username') ?? 'Admin';

        return view('admin.settings', compact('screeningResumeMinutes', 'adminName'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'screening_resume_minutes' => 'required|integer|min:1|max:1440',
        ], [
            'screening_resume_minutes.required' => 'Durasi jeda wajib diisi.',
            'screening_resume_minutes.integer' => 'Durasi jeda harus berupa angka.',
            'screening_resume_minutes.min' => 'Durasi jeda minimal 1 menit.',
            'screening_resume_minutes.max' => 'Durasi jeda maksimal 1440 menit atau 24 jam.',
        ]);

        Setting::setValue('screening_resume_minutes', $request->screening_resume_minutes);

        AdminLog::record(
            'Mengubah pengaturan',
            'Pengaturan',
            'Mengubah durasi jeda screening menjadi ' . $request->screening_resume_minutes . ' menit.'
        );

        return back()->with('success', 'Pengaturan durasi jeda screening berhasil diperbarui.');
    }
}
