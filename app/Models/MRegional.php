<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MRegional extends Model
{
    use HasFactory;

    protected $table = 'master_regional'; // Nama tabel di database

    protected $primaryKey = 'id_regional'; // Primary Key

    public $timestamps = true; // Jika menggunakan created_at dan updated_at

    protected $fillable = [
        'nama_regional',
    ];
}
