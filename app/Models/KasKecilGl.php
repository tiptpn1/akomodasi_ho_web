<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilGl extends Model
{
    use HasFactory;

    protected $table = 'm_gl';
    protected $primaryKey = 'id_gl';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nomor_gl',
        'nama_gl',
    ];
}