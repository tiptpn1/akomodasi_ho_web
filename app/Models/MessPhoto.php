<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessPhoto extends Model
{
    use HasFactory;
    protected $table = 'mess_photos';
    protected $fillable = ['mess_id', 'foto','is_utama'];

    public function mess()
    {
        return $this->belongsTo(MessModel::class);
    }
}
