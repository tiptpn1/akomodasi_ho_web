<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilCc extends Model
{
    use HasFactory;

    protected $table = 'm_cc';
    protected $primaryKey = 'id_cc';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nomor_cc',
        'nama_cc',
    ];
}