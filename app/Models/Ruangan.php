<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // protected $fillable = [
    //     'nama',
    //     'status'
    // ];

    protected $guarded = [
        'id'
    ];

    public function sendVicons()
    {
        return $this->hasMany(SendVicon::class, 'id_ruangan', 'id');
    }

    // public function getDataDistinct($column, $where = null)
    // {
    //     $query = self::select($column)->where('lantai', '>', 0);

    //     if ($where) {
    //         $query->where($where);
    //     }

    //     return $query->distinct()->get();
    // }

    public function getDataDistinct($column, $where = null, $regionalId = null) // Tambahkan $regionalId
{
    $query = self::select($column)->where('lantai', '>', 0);

    if ($where) {
        $query->where($where);
    }

    // Tambahkan kondisi where untuk regional jika $regionalId diberikan
    if ($regionalId) {
        $query->where('ruangan_regional_id', $regionalId);
    }

    return $query->distinct(); // <--- PENTING: Hapus .get() di sini
}

    public function getSpesificData($where, $date = null)
    {
        return self::with(['sendVicons' => function ($q) use ($date) {
            $q->orderBy('status_approval', 'desc');
            if ($date) {
                $q->where('tanggal', $date);
            }
        }, 'sendVicons.jenisrapat', 'sendVicons.bagian'])->where($where);
    }

    public static function cekviconRuanganWaktu($tanggal, $waktu1, $waktu2)
    {
        return self::where('status', 'Aktif')
            ->where('id', '!=', 5)
            ->whereNotIn('id', function ($subQuery) use ($tanggal, $waktu1, $waktu2) {
                $subQuery->select('id_ruangan')
                    ->from('sendvicon')
                    ->where('tanggal', $tanggal)
                    ->where('waktu', '>=', $waktu1)
                    ->where('waktu2', '<=', $waktu2)
                    ->whereNotNull('id_ruangan');
            });
    }

    public function regional() 
    {
        return $this->belongsTo(MRegional::class, 'ruangan_regional_id', 'id_regional');
    }
}
