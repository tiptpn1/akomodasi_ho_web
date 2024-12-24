<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilVendor extends Model
{
    use HasFactory;

    protected $table = 'm_vendor';
    protected $primaryKey = 'id_vendor';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_vendor',
    ];
}
