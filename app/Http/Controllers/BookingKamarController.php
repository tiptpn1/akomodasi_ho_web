<?php 
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingKamar;
use App\Models\Jabatan;
use App\Models\KamarModel;
use App\Models\MessModel;
use App\Models\ReviewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

use App\Models\PetugasMess;
// use Whatsapp;
use App\Facades\Whatsapp; // <- ini penting!


class BookingKamarController extends Controller
{
  
//     public function index(Request $request)
// {
//     $tanggal_mulai = $request->query('tanggal_mulai');
//     $tanggal_selesai = $request->query('tanggal_selesai');

//     // Jika tanggal belum dipilih, kembalikan koleksi kosong
//     if (!$tanggal_mulai || !$tanggal_selesai) {
//         $kamars = collect();
//         return view('kamar.booking', compact('kamars', 'tanggal_mulai', 'tanggal_selesai'));
//     }

//     // Ambil semua kamar
//     $kamars = KamarModel::all()->map(function ($kamar) use ($tanggal_mulai, $tanggal_selesai) {
//         // Hitung jumlah orang yang sudah booking dalam rentang tanggal
//         $jumlahTerbooking = BookingKamar::where('kamar_id', $kamar->id)
//             ->where(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
//                 $query->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
//                       ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
//                       ->orWhere(function ($q) use ($tanggal_mulai, $tanggal_selesai) {
//                           $q->where('tanggal_mulai', '<=', $tanggal_mulai)
//                             ->where('tanggal_selesai', '>=', $tanggal_selesai);
//                       });
//             })
//             ->count();

//         // Hitung sisa kapasitas
//         $sisa_kapasitas = $kamar->kapasitas - $jumlahTerbooking;

//         // Tambahkan info sisa kapasitas ke dalam objek kamar
//         $kamar->sisa_kapasitas = max($sisa_kapasitas, 0);
//         return $kamar;
//     });

//     return view('kamar.booking', compact('kamars', 'tanggal_mulai', 'tanggal_selesai'));
// }

public function index(Request $request)
{
    $tanggal_mulai = $request->query('tanggal_mulai');
    $tanggal_selesai = $request->query('tanggal_selesai');
    $mess_id = $request->query('mess_id', 'all');
    $jabatan_id = $request->query('jabatan_id', 'all');

    // Ambil daftar Mess dan Jabatan untuk filter dropdown
    $messes = MessModel::all();
    $jabatans = Jabatan::all();

    $regionals = [
        'Head Office','Regional 1', 'Regional 2', 'Regional 3', 'Regional 4',
        'Regional 5', 'Regional 6', 'Regional 7', 'Regional 8'
    ];
    
    // Jika tanggal belum dipilih, kembalikan koleksi kosong
    if (!$tanggal_mulai || !$tanggal_selesai) {
        $kamars = collect();
        return view('kamar.booking', compact('kamars', 'tanggal_mulai', 'tanggal_selesai', 'messes', 'jabatans','regionals'));
    }


    $kamars = KamarModel::with('reviews')->when($mess_id !== 'all', function ($query) use ($mess_id) {
        return $query->where('mess_id', $mess_id);
    })
    ->when($jabatan_id !== 'all', function ($query) use ($jabatan_id) {
        return $query->whereRaw("FIND_IN_SET(?, peruntukan)", [$jabatan_id]);
    })
    ->get()
    ->map(function ($kamar) use ($tanggal_mulai, $tanggal_selesai, $jabatans) {
        // Hitung jumlah orang yang sudah booking dalam rentang tanggal
        $jumlahTerbooking = BookingKamar::where('kamar_id', $kamar->id)
            ->where(function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                $query->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                      ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                      ->orWhere(function ($q) use ($tanggal_mulai, $tanggal_selesai) {
                          $q->where('tanggal_mulai', '<=', $tanggal_mulai)
                            ->where('tanggal_selesai', '>=', $tanggal_selesai);
                      });
            })
            ->count();

        // Hitung sisa kapasitas
        $sisa_kapasitas = $kamar->kapasitas - $jumlahTerbooking;
        $kamar->sisa_kapasitas = max($sisa_kapasitas, 0);

        // Hitung rating rata-rata
        $kamar->rating = round($kamar->reviews->avg('rating'), 1) ?? 0;

        // Tambahkan peruntukan_teks
        $peruntukanIds = explode(',', $kamar->peruntukan);
        $namaPeruntukan = $jabatans->whereIn('id', $peruntukanIds)->pluck('jabatan')->toArray();
        $kamar->peruntukan_teks = implode(', ', $namaPeruntukan);
        $kamar->petugas = PetugasMess::where('mess_id', $kamar->mess_id)->get();

        return $kamar;
    });

    return view('kamar.booking', compact('kamars', 'tanggal_mulai', 'tanggal_selesai', 'messes', 'jabatans','regionals'));
}


public function store(Request $request)
{
    try {
        // Cari ID jabatan berdasarkan nama jabatan
        $jabatan = \DB::table('m_jabatan')->where('jabatan', $request->jabatan)->first();
        // dd($request->all());
        if (!$jabatan) {
            return back()->with('error', 'Jabatan tidak valid.');
        }

        // Lakukan validasi dengan ID jabatan yang ditemukan
        $request->merge(['jabatan_id' => $jabatan->id]); // Menambahkan jabatan_id ke request
        // dd($request->merge(['jabatan_id' => $jabatan->id]));
        $request->validate([
            'kamar_id' => 'required|exists:m_kamar,id',
            'nama_pemesan' => 'required|string|max:255',
            'jabatan_id' => 'required|exists:m_jabatan,id', // Validasi menggunakan ID
            'regional' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'catatan' => 'nullable|string',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048', // Maks 2MB
        ]);
        
        // Cek apakah kamar tersedia
        $kamar = KamarModel::findOrFail($request->kamar_id);
        if (!$kamar->isAvailable($request->tanggal_mulai, $request->tanggal_selesai)) {
            return back()->with('error', 'Kamar sudah penuh di tanggal tersebut!');
        }
        // dd($request->jabatan_id);
        // Cek apakah jabatan sesuai dengan peruntukan kamar
        // if ($request->jabatan_id > $kamar->peruntukan) {
        //     return back()->with('error', 'Jabatan Anda tidak memenuhi syarat untuk menempati kamar ini.');
        // }
        // dd($request->all());
        // Upload dokumen jika ada
        // $dokumenPath = null;
        // if ($request->hasFile('dokumen_pendukung')) {
        //     $dokumenPath = $request->file('dokumen_pendukung')->store('dokumen_booking', 'public');
        // }
        $dokumenPath = null;
        // dd($request->file('dokumen_pendukung'));
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan langsung ke folder public/dokumen_booking
            $file->move(public_path('dokumen_booking'), $filename);

            // Simpan path relatif ke DB
            $dokumenPath = 'dokumen_booking/' . $filename;
        }
        // dd($dokumenPath);


        // Simpan booking ke database
        BookingKamar::create([
            'kamar_id' => $request->kamar_id,
            'nama_pemesan' => $request->nama_pemesan,
            'jabatan' => $request->jabatan, // Gunakan ID jabatan
            'regional' => $request->regional,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'catatan' => $request->catatan,
            'dokumen_pendukung' => $dokumenPath, // Simpan path dokumen
            'status' => 'pending',
        ]);

        return back()->with('success', 'Booking berhasil, menunggu konfirmasi!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->validator->errors())->withInput();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return back()->with('error', 'Data tidak ditemukan.');
    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}



    public function cancelBooking(Request $request, $id)
    {
        $request->validate([
            'keterangan_cancel' => 'required|string|max:500'
        ]);

        $booking = BookingKamar::findOrFail($id);
        $booking->update([
            'status' => 'cancelled',
            'keterangan_cancel' => $request->keterangan_cancel
        ]);

        $message = "Halo, {$booking->nama_pemesan} 😊.\n\n"
            . "Booking Anda di *{$booking->kamar->mess->nama_mess}* - *{$booking->kamar->nama_kamar}* dibatalkan! 🎉\n\n"
            . "Silahkan pilih kamar/mess lainnya.\n\n"
            . "Atau hubungi Divisi/Bagian terkait.\n";

            // $no='085275104312';
            // $response = Whatsapp::send($no, $message);
        $response = Whatsapp::send($booking->no_hp, $message);

        return $response->successful()
            ? back()->with('success', 'Booking ditolak dan pesan WhatsApp terkirim.')
            : back()->with('error', 'Booking ditolak tapi gagal mengirim WhatsApp.');

        // return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function list_booking()
    {
        $bookings = BookingKamar::with('kamar.mess')->orderBy('created_at', 'desc')->get();
        return view('kamar.list_booking', compact('bookings'));
    }

    // Proses approve booking (Admin)
    // public function approve($id)
    // {
    //     // $booking = BookingKamar::findOrFail($id);
    //     // $booking->update(['status' => 'approved']);

    //     // return back()->with('success', 'Booking telah disetujui!');
    //     $booking = BookingKamar::findOrFail($id);
    //     $booking->status = 'approved';
    //     $booking->save();

    //     // Buat token review jika belum ada
    //     if (!$booking->review) {
    //         $token = Str::random(32);
    //         ReviewModel::create([
    //             'booking_id' => $booking->id,
    //             'token' => $token,
    //         ]);
    //     } else {
    //         $token = $booking->review->token;
    //     }

    //     // Kirim WhatsApp ke pelanggan
    //     $whatsappMessage = "Halo, " . $booking->nama_pemesan . " 😊.\n\n"
    //         . "Booking Anda di *" . $booking->kamar->mess->nama_mess . "* - *" . $booking->kamar->nama_kamar . "* telah disetujui! 🎉\n\n"
    //         . "Kami sangat menghargai jika Anda bisa memberikan review setelah menginap.\n\n"
    //         . "Klik link berikut untuk memberikan review: " . route('review.show', ['token' => $token]);

    //     // Format URL WhatsApp
    //     $whatsappUrl = "https://wa.me/" . $booking->no_hp . "?text=" . urlencode($whatsappMessage);

    //     return redirect()->away($whatsappUrl);
    // }

//     public function approve($id)
// {
//     $booking = BookingKamar::findOrFail($id);
//     $booking->status = 'approved';
//     $booking->save();

//     // Buat token review jika belum ada
//     if (!$booking->review) {
//         $token = Str::random(32);
//         ReviewModel::create([
//             'booking_id' => $booking->id,
//             'token' => $token,
//         ]);
//     } else {
//         $token = $booking->review->token;
//     }

//     // Pesan WhatsApp
//     $message = "Halo, {$booking->nama_pemesan} 😊.\n\n"
//         . "Booking Anda di *{$booking->kamar->mess->nama_mess}* - *{$booking->kamar->nama_kamar}* telah disetujui! 🎉\n\n"
//         . "Kami sangat menghargai jika Anda bisa memberikan review setelah menginap.\n\n"
//         . "Klik link berikut untuk memberikan review:\n"
//         . route('review.show', ['token' => $token]);

//     // Kirim via Fonnte
//     $response = Http::withHeaders([
//         'Authorization' => 'iEYu7vFuH665Ed8TiNge',
//     ])->asForm()->post('https://api.fonnte.com/send', [
//         'target' => $booking->no_hp,
//         // 'target' => '085275104312',
//         'message' => $message,
//         'countryCode' => '62', // opsional
//     ]);

//     if ($response->successful()) {
//         return back()->with('success', 'Booking disetujui dan pesan WhatsApp terkirim.');
//     } else {
//         return back()->with('error', 'Booking disetujui tapi gagal mengirim WhatsApp.');
//     }
// }



    public function approve($id)
    {
        $booking = BookingKamar::findOrFail($id);
        $booking->status = 'approved';
        $booking->save();
        $id_mess =$booking->kamar->mess->id; 
        $petugas= PetugasMess::where('mess_id', $id_mess)->get();
        // dd($petugas);
        foreach ($petugas as $p) {
            $message = "Halo, {$p->nama_petugas} 😊.\n\n"
            . "Karyawan atas nama {$booking->nama_pemesan} yang akan menginap di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah disetujui! 🎉\n\n"
            . "Mohon dipersiapkan untuk kamar dan perlengkapan yang dibutuhkan.\n\n";
            Whatsapp::send($p->no_petugas, $message);
        }

        $daftarPetugas = '';
        foreach ($petugas as $p) {
            $daftarPetugas .= "- {$p->nama_petugas} ({$p->no_petugas})\n";
        }
        
        $message1 = "Halo, {$booking->nama_pemesan} 😊.\n\n"
            . "Pemesanan kamar di *{$booking->kamar->mess->nama_mess}* - *{$booking->kamar->nama_kamar}* telah disetujui! 🎉\n\n"
            . "Jika ada sesuatu yang dibutuhkan dapat berkoordinasi dengan petugas mess:\n\n"
            . $daftarPetugas;

           
        $response = Whatsapp::send($booking->no_hp, $message1);

        return $response->successful()
            ? back()->with('success', 'Booking disetujui dan pesan WhatsApp terkirim.')
            : back()->with('error', 'Booking disetujui tapi gagal mengirim WhatsApp.');
    }

    public function checkout($id)
    {
        $booking = BookingKamar::findOrFail($id);
        $booking->status = 'checked_out';
        $booking->save();
        $token = $booking->review->token ?? Str::random(32);
        if (!$booking->review) {
            ReviewModel::create([
                'booking_id' => $booking->id,
                'token' => $token,
            ]);
        }

        $message = "Halo, {$booking->nama_pemesan} 😊.\n\n"
            . "Anda telah berhasil Check Out dari *{$booking->kamar->mess->nama_mess}* - *{$booking->kamar->nama_kamar}* 🎉\n\n"
            . "Kami sangat menghargai jika Anda bisa memberikan review setelah menginap.\n\n"
            . "Klik link berikut untuk memberikan review:\n"
            . route('review.show', ['token' => $token]);

            // $no='085275104312';
            // $response = Whatsapp::send($no, $message);
        $response = Whatsapp::send($booking->no_hp, $message);

        return $response->successful()
            ? back()->with('success', 'Checkout berhasil dan pesan WhatsApp terkirim.')
            : back()->with('error', 'Checkout berhasil tapi gagal mengirim WhatsApp.');

        // return back()->with('success', 'Checkout berhasil!');
    }


    // Proses reject booking (Admin)
    public function reject(Request $request, $id)
    {
        $booking = BookingKamar::findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'keterangan' => $request->alasan_reject
        ]);

        return back()->with('success', 'Booking telah ditolak!');
    }

    // Proses cancel booking (User)
    public function cancel(Request $request, $id)
    {
        $booking = BookingKamar::findOrFail($id);
        if ($booking->status == 'pending') {
            $booking->update([
                'status' => 'cancelled',
                'catatan' => $request->keterangan_cancel
            ]);

            return back()->with('success', 'Booking berhasil dibatalkan!');
        }

        return back()->with('error', 'Booking tidak dapat dibatalkan!');
    }

    public function perpanjangan(Request $request, $id)
    {
        // dd('asdsad');
        $request->validate([
            'tanggal_selesai_baru' => 'required|date|after:today',
        ]);
    
        // $booking = BookingKamar::findOrFail($id);
        // $tanggalLama = $booking->tanggal_selesai;
        // $tanggalBaru = $request->tanggal_selesai_baru;
        // $idKamar = $booking->kamar_id;
    
        // // Cek apakah ada booking lain untuk kamar yang sama di antara tanggal tersebut
        // $conflict = DB::table('booking_kamar')
        //     ->where('kamar_id', $idKamar)
        //     ->where('id', '!=', $id) // Hindari current booking
        //     ->where(function ($query) use ($tanggalLama, $tanggalBaru) {
        //         $query->whereBetween('tanggal_mulai', [$tanggalLama, $tanggalBaru])
        //               ->orWhereBetween('tanggal_selesai', [$tanggalLama, $tanggalBaru])
        //               ->orWhere(function ($query2) use ($tanggalLama, $tanggalBaru) {
        //                   $query2->where('tanggal_mulai', '<=', $tanggalLama)
        //                          ->where('tanggal_selesai', '>=', $tanggalBaru);
        //               });
        //     })
        //     ->exists();
    
        // if ($conflict) {
        //     return back()->with('error', 'Perpanjangan gagal! Tanggal tersebut sudah dibooking oleh pengguna lain.');
        // }
    
        // // Jika tidak ada konflik, update tanggal_selesai
        // $booking->update([
        //     'tanggal_selesai' => $tanggalBaru,
        //     'keterangan' => 'Diperpanjang sampai ' . $tanggalBaru
        // ]);
    
        // return back()->with('success', 'Perpanjangan berhasil!');
        $booking = BookingKamar::findOrFail($id);

        $tanggalMulaiBaru = Carbon::parse($booking->tanggal_selesai)->addDay();
        $tanggalSelesaiBaru = Carbon::parse($request->tanggal_selesai_baru);
        $kapasitas = $booking->kamar->kapasitas;

        $tanggalIterasi = clone $tanggalMulaiBaru;

        while ($tanggalIterasi->lte($tanggalSelesaiBaru)) {
            $jumlahBookingLain = BookingKamar::where('kamar_id', $booking->kamar_id)
                ->where('status', 'Approved')
                ->where('id', '!=', $booking->id)
                ->where('tanggal_mulai', '<=', $tanggalIterasi)
                ->where('tanggal_selesai', '>=', $tanggalIterasi)
                ->count();

            // hanya tambahkan 1 jika booking saat ini juga 'Approved'
            $totalPenghuni = $jumlahBookingLain + ($booking->status === 'Approved' ? 1 : 0);

            \Log::info("Tanggal {$tanggalIterasi->toDateString()}: Jumlah booking lain = {$jumlahBookingLain}, Total = {$totalPenghuni}/{$kapasitas}");

            if ($totalPenghuni >= $kapasitas) {
                return back()->with('error', 'Gagal perpanjang. Kamar sudah penuh pada tanggal ' . $tanggalIterasi->toDateString());
            }

            $tanggalIterasi->addDay();
        }

        // Jika aman, lakukan update
        $booking->update([
            'tanggal_selesai' => $tanggalSelesaiBaru,
            'keterangan' => 'Diperpanjang sampai ' . $tanggalSelesaiBaru->toDateString(),
        ]);

        // return back()->with('success', 'Perpanjangan berhasil!');
        $id_mess =$booking->kamar->mess->id; 
        $petugas= PetugasMess::where('mess_id', $id_mess)->get();
        // dd($petugas);
        foreach ($petugas as $p) {
            $message = "Halo, {$p->nama_petugas} 😊.\n\n"
            . "Karyawan atas nama {$booking->nama_pemesan} yang akan menginap di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah disetujui! 🎉\n\n"
            . "Mohon dipersiapkan untuk kamar dan perlengkapan yang dibutuhkan.\n\n";
            Whatsapp::send($p->no_petugas, $message);
        }

        $daftarPetugas = '';
        foreach ($petugas as $p) {
            $daftarPetugas .= "- {$p->nama_petugas} ({$p->no_petugas})\n";
        }
        
        $message1 = "Halo, {$booking->nama_pemesan} 😊.\n\n"
            . "Pemesanan kamar di *{$booking->kamar->mess->nama_mess}* - *{$booking->kamar->nama_kamar}* telah disetujui! 🎉\n\n"
            . "Jika ada sesuatu yang dibutuhkan dapat berkoordinasi dengan petugas mess:\n\n"
            . $daftarPetugas;

           
        $response = Whatsapp::send($booking->no_hp, $message1);

        return $response->successful()
            ? back()->with('success', 'Booking disetujui dan pesan WhatsApp terkirim.')
            : back()->with('error', 'Booking disetujui tapi gagal mengirim WhatsApp.');
    }

}
