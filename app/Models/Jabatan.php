<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    protected $table = 'm_jabatan';
    // protected $fillable = ['id', 'jabatan', 'status'];
    protected $primaryKey = 'id';
    public $timestamps = false; // Jika tabel tidak memiliki created_at & updated_at


    // public function photos()
    // {
    //     return $this->hasMany(KamarPhoto::class);
    // }

    // public function mess()
    // {
    //     return $this->belongsTo(MessModel::class);
    // }
}
