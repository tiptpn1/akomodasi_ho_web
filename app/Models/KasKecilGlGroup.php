<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasKecilGlGroup extends Model
{
    use HasFactory;

    protected $table = 'm_group_gl';
    protected $primaryKey = 'id_group_gl';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'nomor_gl',
    ];
}