<?php

namespace App\Jobs;

use App\Facades\Whatsapp;
use App\Models\BookingKamar;
use App\Models\PetugasMess;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SendWhatsappNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $booking;
    protected $status;
    protected $keterangan;
    protected $token;
    protected $user;

    public function __construct(BookingKamar $booking, string $status, string $keterangan = null, string $token = null, User $user)
    {
        $this->booking = $booking;
        $this->status = $status;
        $this->keterangan = $keterangan;
        $this->token = $token;
        $this->user = $user; 
    }
    
    /**
     * Execute the job.
     */
public function handle()
    {
        $booking = $this->booking;
        $status = $this->status;

        $adminMessage = "Halo {$this->user->master_user_nama} 😊.\n\n"
            . "Booking kamar atas nama {$booking->nama_pemesan} di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah *Di " . ucfirst($status) . "*.\n"
            . "Tanggal menginap: {$booking->tanggal_mulai} s.d. {$booking->tanggal_selesai}.\n";
        
        $petugasMessage = '';
        $userMessage = '';

        switch ($status) {
            case 'pending':
                $userMessage = "Halo, {$booking->nama_pemesan} 😊.\n\n"
                    . "Pemesanan kamar di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* untuk tanggal {$booking->tanggal_mulai} s.d. {$booking->tanggal_selesai} telah berhasil kami terima! 🎉\n\n"
                    . "Pemesanan Anda akan segera diproses oleh admin. Mohon menunggu konfirmasi.\n\n"
                    . "Terima kasih.";

                $adminMessage = "Halo, admin 😊.\n\n"
                    . "Ada pengajuan booking kamar baru dari {$booking->nama_pemesan} di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}*.\n"
                    . "Tanggal: {$booking->tanggal_mulai} s.d. {$booking->tanggal_selesai}.\n\n"
                    . "Segera cek dan proses pengajuan ini di ARHAN.";
                break;

            case 'approved':
                // Logika untuk notifikasi 'approved'
                $id_mess = $booking->kamar->mess->id;
                $petugas = PetugasMess::where('mess_id', $id_mess)->get();
                $daftarPetugas = '';

                foreach ($petugas as $p) {
                    $daftarPetugas .= "- {$p->nama_petugas} ({$p->no_petugas})\n";
                    $petugasMessage = "Halo, {$p->nama_petugas} 😊.\n\n"
                        . "Karyawan atas nama {$booking->nama_pemesan} ({$booking->regional}) yang akan menginap di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* untuk tanggal {$booking->tanggal_mulai} s.d. {$booking->tanggal_selesai} telah disetujui! 🎉\n\n"
                        . "Mohon dipersiapkan untuk kamar dan perlengkapan yang dibutuhkan.\n\n";
                    Whatsapp::send($p->no_petugas, $petugasMessage);
                }

                $userMessage = "Halo, {$booking->nama_pemesan} dari {$booking->regional} 😊.\n\n"
                    . "Pemesanan kamar di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* untuk tanggal {$booking->tanggal_mulai} s.d. {$booking->tanggal_selesai} telah disetujui! 🎉\n\n"
                    . "Jika ada sesuatu yang dibutuhkan dapat berkoordinasi dengan petugas mess:\n\n"
                    . $daftarPetugas;
                $adminMessage .= "Lihat detail di ARHAN untuk informasi lebih lanjut.";
                break;

            case 'rejected':
                $userMessage = "Halo, {$booking->nama_pemesan} 🙏.\n\n"
                    . "Mohon maaf, pemesanan Anda pada *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* tidak disetujui dengan keterangan:\n{$this->keterangan}\n\n";
                $adminMessage .= "Keterangan: {$this->keterangan}\n\n"
                    . "Lihat detail di ARHAN untuk informasi lebih lanjut.";
                break;

            case 'checked_out':
                $userMessage = "Halo, {$booking->nama_pemesan} 😊.\n\n"
                    . "Anda telah berhasil Check Out dari *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}*.\n\n"
                    . "Kami sangat menghargai jika Anda bisa memberikan review setelah menginap:\n"
                    . route('review.show', ['token' => $this->token]);
                $adminMessage .= "Silahkan buka ARHAN untuk monitoring data tersebut.\n\n";

                $id_mess = $booking->kamar->mess->id;
                $petugas = PetugasMess::where('mess_id', $id_mess)->get();
                foreach ($petugas as $p) {
                    $petugasMessage = "Halo, {$p->nama_petugas} 😊.\n\n"
                        . "Karyawan atas nama {$booking->nama_pemesan} yang menginap di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah check out! \n\n"
                        . "Kamar dapat segera dibersihkan.\n\n";
                    Whatsapp::send($p->no_petugas, $petugasMessage);
                }
                break;

            case 'perpanjangan':
                $userMessage = "Halo, {$booking->nama_pemesan} 😊.\n\n"
                    . "Masa menginap Anda di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah berhasil diperpanjang hingga tanggal {$booking->tanggal_selesai}! 🎉\n\n"
                    . "Semoga betah.\n\n";
                $adminMessage .= "Keterangan: Perpanjangan booking hingga tanggal {$booking->tanggal_selesai}.\n\n"
                    . "Lihat detail di ARHAN untuk informasi lebih lanjut.";
                
                $id_mess = $booking->kamar->mess->id;
                $petugas = PetugasMess::where('mess_id', $id_mess)->get();
                foreach ($petugas as $p) {
                    $petugasMessage = "Halo, {$p->nama_petugas} 😊.\n\n"
                        . "Masa menginap atas nama {$booking->nama_pemesan} di *{$booking->kamar->mess->nama}* - *{$booking->kamar->nama_kamar}* telah diperpanjang hingga tanggal {$booking->tanggal_selesai}!\n\n"
                        . "Mohon untuk diperhatikan.\n\n";
                    Whatsapp::send($p->no_petugas, $petugasMessage);
                }
                break;
        }

        // Kirim notifikasi ke user
        if ($userMessage) {
            Whatsapp::send($booking->no_hp, $userMessage);
        }

        // Kirim notifikasi ke admin
        $admins = User::where('master_hak_akses_id', 2)->get();
        foreach ($admins as $admin) {
            Whatsapp::send($admin->master_user_no_hp, $adminMessage);
        }
    }
}