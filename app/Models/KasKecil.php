<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasKecil extends Model
{
    use HasFactory;

    protected $table = 'd_kaskecil';
    protected $primaryKey = 'id_kaskecil';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_pengaju',
        'tgl_pengajuan',
        'id_group',
        'nomor_gl',
        'nomor_cc',
        'id_kendaraan',
        'km_awal',
        'km_akhir',
        'id_bbm',
        'km_awal',
        'liter_bensin',
        'harga_bensin',
        'nominal',
        'ppn',
        'pph',
        'tol',
        'parkir',
        'biaya_aplikasi',
        'lain_lain',
        'dibayarkan_oleh',
        'tgl_dibayarkan',
        'bukti_nota',
        'bukti_bayar',
        'keterangan',
    ];

    protected $guarded = [
        'id_kaskeci'
    ];

    public function group()
    {
        return $this->belongsTo(KasKecilGroup::class, 'id_group', 'id_group');
    }

    public function gl()
    {
        return $this->belongsTo(KasKecilGl::class, 'nomor_gl', 'nomor_gl');
    }

    public function cc()
    {
        return $this->belongsTo(KasKecilCc::class, 'nomor_cc', 'nomor_cc');
    }

    public function bbm()
    {
        return $this->belongsTo(KasKecilBbm::class, 'id_bbm', 'id_bbm');
    }
    public function kendaraan()
    {
        return $this->belongsTo(KasKecilKendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
