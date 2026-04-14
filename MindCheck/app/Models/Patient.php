<?php // Patient.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $primaryKey = 'token';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['token'];

    public function screenings()
    {
        return $this->hasMany(Screening::class, 'patient_token', 'token');
    }
}
