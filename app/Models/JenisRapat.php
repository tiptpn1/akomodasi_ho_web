<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisRapat extends Model
{
    use HasFactory;

    protected $table = 'jenisrapat';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'status',
        'keterangan'
    ];

    protected $guarded = [
        'id'
    ];

    public function sendVicons()
    {
        return $this->hasMany(SendVicon::class, 'jenisrapat_id', 'id');
    }
}
