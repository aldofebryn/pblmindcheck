<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_name',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];

    public static function record(string $action, ?string $module = null, ?string $description = null)
    {
        return self::create([
            'admin_id' => session('admin_id'),
            'admin_name' => session('admin_name') ?? session('admin_username') ?? 'Admin',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
