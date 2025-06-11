<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'divisi',
        'nama_pic',
        'tgl_berangkat',
        'jam_berangkat',
        'jam_kembali',
        'jenis_tujuan',
        'tujuan',
        'pejemputan',
        'file_memo',
        'driver',
        'rental_driver',
        'rental_kendaraan',
        'no_polisi',
        'status',
        'username',
        'apprv',
        'created_at',
        'updated_at',
        'ket',
    ];

    // Relasi ke MDriver (driver = id_driver)
    public function driverDetail()
    {
        return $this->belongsTo(MDriver::class, 'driver', 'id_driver');
    }

    // Relasi ke MKendaraan (no_polisi = id_kendaraan)
    public function kendaraanDetail()
    {
        return $this->belongsTo(MKendaraan::class, 'no_polisi', 'id_kendaraan');
    }
}
