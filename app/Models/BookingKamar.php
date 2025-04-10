<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingKamar extends Model
{
    use HasFactory;
    protected $table = 'booking_kamar';
    protected $fillable = [
        'kamar_id', 'nama_pemesan', 'email', 'no_hp', 'tanggal_mulai', 'tanggal_selesai', 'catatan', 'status','dokumen_pendukung','keterangan'
        ,'jabatan','regional'];

    public function kamar()
    {
        return $this->belongsTo(KamarModel::class, 'kamar_id');
    }
    public function mess()
    {
        return $this->hasOneThrough(MessModel::class, KamarModel::class, 'id', 'id', 'kamar_id', 'mess_id');
    }
}
