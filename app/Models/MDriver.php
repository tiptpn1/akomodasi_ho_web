<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class MDriver extends Model
// {
//     use HasFactory;

//     protected $table = 'm_driver'; // Nama tabel di database

//     protected $primaryKey = 'id_driver'; // Primary Key

//     public $timestamps = true; // Jika menggunakan created_at dan updated_at

//     protected $fillable = [
//         'nama_driver',
//         'no_hp',
//     ];
// }



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDriver extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'm_driver';

    // Kunci utama (Primary Key)
    protected $primaryKey = 'id_driver';

    // Mengaktifkan penggunaan timestamps (created_at dan updated_at)
    public $timestamps = true;

    protected $fillable = [
        'driver_regional_id',
        'nama_driver',
        'no_hp',
    ];

    /**
     * Mendefinisikan relasi one-to-many dengan model PKendaraan.
     * Satu driver dapat memiliki banyak perjalanan.
     */
    public function p_kendaraans()
    {
        // 'driver' adalah foreign key di tabel 'kendaraan'.
        // 'id_driver' adalah primary key di tabel 'm_driver' ini.
        return $this->hasMany(PKendaraan::class, 'driver', 'id_driver');
    }
}

