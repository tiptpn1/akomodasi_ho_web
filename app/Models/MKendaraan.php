<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MKendaraan extends Model
{
    use HasFactory;

    protected $table = 'master_kendaraan'; // Nama tabel di database

    protected $primaryKey = 'id_kendaraan'; // Primary Key

    public $timestamps = true; // Jika menggunakan created_at dan updated_at

    protected $fillable = [
        'kendaraan_regional_id',
        'nopol',
        'tipe_kendaraan',
        'kepemilikan',
        'foto',
    ];
}
