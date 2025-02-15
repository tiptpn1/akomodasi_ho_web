<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'sendvicon_id',
        'nama',
        'jabatan',
        'instansi',
        'ip',
        'city',
        'region',
        'country',
        'loc',
        'timezone',
        'browser',
        'os',
    ];

    protected $guarded = [
        'id'
    ];

    public function sendvicon()
    {
        return $this->belongsTo(SendVicon::class, 'sendvicon_id', 'id');
    }
}
