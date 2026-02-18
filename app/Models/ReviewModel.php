<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewModel extends Model
{
    use HasFactory;

    protected $table = 'reviews';
    
    protected $fillable = [
        'booking_id',
        'token',
        'rating',
        'review',
        'is_used',
    ];

    /**
     * Relasi ke model BookingKamar
     */
    public function booking()
    {
        return $this->belongsTo(BookingKamar::class, 'booking_id');
    }
}
