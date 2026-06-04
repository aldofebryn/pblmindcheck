<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\GuardsAdmin;

class InfoController extends Controller
{
    use GuardsAdmin;

    public function index()
    {
        $this->guardAdmin();

        $adminName = session('admin_name') ?? session('admin_username') ?? 'Admin';

        return view('admin.info', compact('adminName'));
    }
}
