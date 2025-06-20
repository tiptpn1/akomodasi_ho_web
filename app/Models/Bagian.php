<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;

    protected $table = 'master_bagian';
    protected $primaryKey = 'master_bagian_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // protected $fillable = [
    //     'bagian',
    //     'status',
    //     'keterangan_bagian',
    //     'kode_pin'
    // ];

    protected $guarded = [
        'master_bagian_id'
    ];

    protected $appends = ['id', 'bagian'];

    public function getBagianAttribute()
    {
        return $this->attributes['master_bagian_nama'];
    }

    public function getIdAttribute()
    {
        return $this->attributes['master_bagian_id'];
    }

    public function sendVicons()
    {
        return $this->hasMany(SendVicon::class, 'bagian_id', 'master_bagian_id');
    }

    public function agendaKendaraans()
    {
        return $this->hasMany(AgendaKendaraan::class, 'id_bagian', 'master_bagian_id');
    }

    public function regional() 
    {
        return $this->belongsTo(MRegional::class, 'bagian_regional_id', 'id_regional');
    }
}
