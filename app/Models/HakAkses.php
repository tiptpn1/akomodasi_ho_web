<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    use HasFactory;

    protected $table = 'master_hak_akses';
    protected $primaryKey = 'hak_akses_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'hak_akses_nama',
        'status'
    ];

    protected $guarded = ['hak_akses_id'];

    public function users()
    {
        return $this->hasMany(User::class, 'master_hak_akses_id', 'hak_akses_id');
    }
}
