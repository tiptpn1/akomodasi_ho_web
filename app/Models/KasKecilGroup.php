<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilGroup extends Model
{
    use HasFactory;

    protected $table = 'm_group';
    protected $primaryKey = 'id_group';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_group',
    ];
}
