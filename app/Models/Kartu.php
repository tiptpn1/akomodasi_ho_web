<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kartu extends Model
{
    use HasFactory;

    protected $table = 'm_kartu';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['nama_pengaju',
                        'nik_karyawan',
                        'divisi',
                        'lift',
                        'parkir',
                        'k1',
                        'k2',
                        'nopol1',
                        'nopol2',
                        'stnk1_file',
                        'stnk2_file',
                        'ktp_file',
                        'memo_file',
                        'status_lift',
                        'status_parkir',
                        'created_at',
                        'updated_at',
                        'apprv_by',
                        'no_lift',
                        'no_parkir'

    ];
}
