@component('mail::message')
# Halo, {{ $booking->nama_pemesan }} 😊

Booking Anda di **{{ $booking->kamar->mess->nama_mess }}** - **{{ $booking->kamar->nama_kamar }}** telah *disetujui*! 🎉

Kami sangat menghargai jika Anda bisa memberikan review setelah menginap.

@component('mail::button', ['url' => route('review.show', ['token' => $token])])
Berikan Review
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
