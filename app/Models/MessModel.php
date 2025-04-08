<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessModel extends Model
{
    use HasFactory;
    protected $table = 'm_mess';
    protected $fillable = ['nama', 'lokasi', 'deskripsi','status','cp','no_cp'];

    public function photos()
    {
        return $this->hasMany(MessPhoto::class,'mess_id');
    }

    public function rooms()
    {
        return $this->hasMany(KamarModel::class);
    }
}
