<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'sendvicon_id',
        'petugas_ti',
        'laptop_vicon',
        'kendala_ti',
        'kendala_umum',
        'kendala_ex',
    ];

    protected $guarded = [
        'id'
    ];

    public function sendVicon()
    {
        return $this->belongsTo(SendVicon::class, 'sendvicon_id', 'id');
    }
}
