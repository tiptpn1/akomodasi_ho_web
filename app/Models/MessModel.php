<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessModel extends Model
{
    use HasFactory;
    protected $table = 'm_mess';
    protected $fillable = ['nama', 'lokasi', 'deskripsi','status','lat', 'lng', 'jarak', 'waktu', 'last_distance_sync'];

    public function photos()
    {
        return $this->hasMany(MessPhoto::class,'mess_id');
    }

    public function rooms()
    {
        return $this->hasMany(KamarModel::class);
    }
    public function petugas()
{
    return $this->hasMany(PetugasMess::class, 'mess_id');
}
}
