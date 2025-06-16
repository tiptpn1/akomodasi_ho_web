<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarModel extends Model
{
    use HasFactory;
    protected $table = 'm_kamar';
    protected $fillable = ['mess_id', 'nama_kamar', 'kapasitas', 'fasilitas','peruntukan','status'];

    public function photos()
    {
        return $this->hasMany(KamarPhoto::class, 'kamar_id');
    }

    public function mess()
    {
        return $this->belongsTo(MessModel::class);
    }
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class,'peruntukan','id');
    }
    public function bookings()
    {
        return $this->hasMany(BookingKamar::class,'kamar_id');
    }
    public function reviews()
    {
        return $this->hasManyThrough(
            ReviewModel::class, 
            BookingKamar::class,
            'kamar_id',  // Foreign key di `booking_kamar`
            'booking_id', // Foreign key di `reviews`
            'id', // Primary key di `kamar`
            'id'  // Primary key di `booking_kamar`
        );
    }

    public function averageRating()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0;
    }
    public function isAvailable($tanggal_mulai, $tanggal_selesai)
    {
        $bookedCount = $this->bookings()
            ->where(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                $query->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                    ->orWhere(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                        $query->where('tanggal_mulai', '<=', $tanggal_mulai)
                                ->where('tanggal_selesai', '>=', $tanggal_selesai);
                    });
            })
            ->count();

        return $bookedCount < $this->kapasitas;
    }

}
