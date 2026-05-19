<?php

namespace App\Http\Controllers\Concerns;

trait GuardsAdmin
{
    private function guardAdmin(): void
    {
        if (! session('admin_id')) {
            abort(redirect()->route('admin.login'));
        }
    }
}
