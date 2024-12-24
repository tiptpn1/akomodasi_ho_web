<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilDriver extends Model
{
    use HasFactory;

    protected $table = 'm_driver';
    protected $primaryKey = 'id_driver';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_driver',
    ];
}