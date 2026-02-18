<?php 
namespace App\Http\Controllers;
use App\Models\Review;
use App\Models\ReviewModel;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    
    // public function store(Request $request, $token)
    // {
    //     $request->validate([
    //         'rating' => 'required|integer|min:1|max:5',
    //         'review' => 'nullable|string',
    //     ]);

    //     $review = ReviewModel::where('token', $token)->firstOrFail();
    //     $review->update([
    //         'rating' => $request->rating,
    //         'review' => $request->review,
    //     ]);

    //     return redirect()->route('review.show', ['token' => $token])->with('success', 'Terima kasih atas review Anda!');
    // }
    public function store(Request $request, $token)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $review = ReviewModel::where('token', $token)->firstOrFail();

        // Cek apakah review sudah diisi
        if ($review->is_used) {
            abort(403, 'Link review ini sudah tidak valid.');
        }

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'is_used' => true, // Tandai token sebagai sudah digunakan
        ]);

        // return redirect()->route('review.success')->with('success', 'Terima kasih atas review Anda!');
        return view('reviews.success');
        
    }


    // public function show($token)
    // {
    //     $review = ReviewModel::where('token', $token)->firstOrFail();
        
    //     // Pastikan hanya booking yang sudah diapprove yang bisa review
    //     if ($review->booking->status !== 'approved') {
    //         abort(403, 'Anda hanya bisa memberikan review jika booking telah disetujui.');
    //     }

    //     return view('reviews.form', compact('review'));
    // }
    public function show($token)
{
    $review = ReviewModel::where('token', $token)->firstOrFail();

    // Cek apakah review sudah diisi
    if ($review->is_used) {
        abort(403, 'Link review ini sudah tidak valid.');
    }

    // Pastikan hanya booking yang sudah diapprove yang bisa review
    // if ($review->booking->status !== 'approved' || $review->booking->status !== 'checked_out') {
    //     abort(403, 'Anda hanya bisa memberikan review jika booking telah disetujui.');
    // }

    return view('reviews.form', compact('review'));
}

}
