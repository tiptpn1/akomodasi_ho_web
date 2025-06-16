<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KamarPhoto extends Model
{
    use HasFactory;
    protected $fillable = ['kamar_id', 'foto','is_utama'];

    public function room()
    {
        return $this->belongsTo(KamarModel::class);
    }
}
