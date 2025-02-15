<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilBbm extends Model
{
    use HasFactory;

    protected $table = 'm_bbm';
    protected $primaryKey = 'id_bbm';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_bbm',
    ];
}