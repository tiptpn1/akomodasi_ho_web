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

    public function getDataDistinct($column, $where = null)
    {
        $query = self::select($column)->where('lantai', '>', 0);

        if ($where) {
            $query->where($where);
        }

        return $query->distinct()->get();
    }

    public function getSpesificData($where)
    {
        return self::with(['sendVicons', 'sendVicons.jenisrapat', 'sendVicons.bagian'])->where($where)->get();
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
}
