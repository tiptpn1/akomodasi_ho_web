<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaDireksi extends Model
{
    use HasFactory;

    protected $table = 'master_agenda_direksi';
    protected $primaryKey = 'id_agenda_dir';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_agenda_dir',
        'kategori_agenda_dir',
        'status_agenda_dir'
    ];
}