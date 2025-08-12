<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;

    protected $table = 'master_regional';
    protected $primaryKey = 'id_regional';
    protected $keyType = 'int';
    public $timestamps = true; 
    protected $guarded = ['hak_akses_id'];

    protected $fillable = [
        'id_regional',
        'nama_regional',
    ];
    
    public function bookingKamar()
    {
        return $this->hasMany(BookingKamar::class, 'regional', 'id_regional');
    }
}
