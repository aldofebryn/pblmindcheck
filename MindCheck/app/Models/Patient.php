<?php // Patient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = ['alias', 'admin_notes', 'username', 'password', 'umur', 'status_pekerjaan'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function screenings()
    {
        return $this->hasMany(Screening::class);
    }
}
