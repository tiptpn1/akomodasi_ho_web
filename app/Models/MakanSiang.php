<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakanSiang extends Model
{
    use HasFactory;

    protected $table = 'm_makansiang';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'tgl_permintaan',
        'nama_pic',
        'divisi',
        'kadiv',
        'jlh_karyawan',
        'jlh_makan',
        'apprv',
        'created_at',
        'updated_at',
        'apprv_at',
    ];
}
