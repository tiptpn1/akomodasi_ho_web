<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDriver extends Model
{
    use HasFactory;

    protected $table = 'm_driver'; // Nama tabel di database

    protected $primaryKey = 'id_driver'; // Primary Key

    public $timestamps = true; // Jika menggunakan created_at dan updated_at

    protected $fillable = [
        'nama_driver',
        'no_hp',
    ];
}
