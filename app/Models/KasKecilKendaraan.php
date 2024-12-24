<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilKendaraan extends Model
{
    use HasFactory;

    protected $table = 'm_kendaraan';
    protected $primaryKey = 'id_kendaraan';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nopol',
        'tipe_kendaraan',
        'kepemilikan',
    ];
}