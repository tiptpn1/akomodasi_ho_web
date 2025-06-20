<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'master_user';
    protected $primaryKey = 'master_user_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // protected $fillable = [
    //     'username',
    //     'petugas',
    //     'role',
    //     'nik',
    //     'no_hp',
    //     'status',
    //     'keterangan',
    //     'password',
    // ];

    protected $guarded = ['master_user_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function agendaKendaraans()
    {
        return $this->hasMany(AgendaKendaraan::class, 'id_user', 'master_user_id');
    }

    public function hakAkses()
    {
        return $this->belongsTo(HakAkses::class, 'master_hak_akses_id', 'hak_akses_id');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'master_nama_bagian_id', 'master_bagian_id');
    }

    public function regional()
    {
        return $this->belongsTo(Bagian::class, 'bagian_regional_id', 'master_bagian_id');
    }
}
