<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;

class SettingsController extends Controller
{
    use GuardsAdmin;

    public function index()
    {
        $this->guardAdmin();
        $adminName = session('admin_name');
        return view('admin.settings', compact('adminName'));
    }
}
