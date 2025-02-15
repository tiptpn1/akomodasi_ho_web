<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsumsi extends Model
{
    use HasFactory;
    // Nama tabel
    protected $table = 'konsumsi';

    // Kolom yang dapat diisi
    protected $fillable = [
        'id_sendvicon',
        'm_pagi',
        'm_siang',
        'm_malam',
        'biaya_m_pagi',
        'biaya_m_siang',
        'biaya_m_malam',
        's_pagi',
        's_siang',
        's_sore',
        'biaya_s_pagi',
        'biaya_s_siang',
        'biaya_s_sore',
        'biaya_lain',
        'keterangan',
        'status',
        'konsumsi_kirim',
        'status_batal_m_pagi',
        'status_batal_m_siang',
        'status_batal_m_malam',
        'status_batal_s_pagi',
        'status_batal_s_siang',
        'status_batal_s_sore',
        'created_at',
        'updated_at',
    ];

    // Definisikan relasi dengan model SendVicon
    public function sendVicon()
    {
        return $this->belongsTo(SendVicon::class, 'id_sendvicon');
    }
}
