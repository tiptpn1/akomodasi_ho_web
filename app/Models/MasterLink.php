<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLink extends Model
{
    use HasFactory;

    protected $table = 'masterlink';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'namalink',
        'link',
        'status'
    ];

    protected $guarded = [
        'id'
    ];

    public function sendVicons()
    {
        return $this->hasMany(SendVicon::class, 'link', 'namalink');
    }
}
