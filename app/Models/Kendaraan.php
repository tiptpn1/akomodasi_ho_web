<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'no_polisi',
        'status',
        'keterangan'
    ];

    protected $guarded = [
        'id'
    ];

    public function agendaKendaraans()
    {
        return $this->hasMany(AgendaKendaraan::class, 'id_kendaraan', 'id');
    }
}
