<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminLog::query()->latest();

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('admin_name', 'like', '%' . $request->search . '%')
                  ->orWhere('action', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('search') || $request->filled('module')) {
            AdminLog::record(
                'Filter log riwayat',
                'Log Riwayat',
                'Admin memfilter log riwayat.'
            );
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('admin.logs', compact('logs'));
    }
}
