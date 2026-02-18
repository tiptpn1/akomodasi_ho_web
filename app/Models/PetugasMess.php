<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class PetugasMess extends Model
    {
        protected $table = 'petugas_mess';
        protected $fillable = ['mess_id', 'nama_petugas', 'no_petugas','created_at','updated_at'];

        public function mess()
        {
            return $this->belongsTo(MessModel::class, 'mess_id');
        }
    }