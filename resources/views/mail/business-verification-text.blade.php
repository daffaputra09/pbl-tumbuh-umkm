Tumbuh UMKM

@if ($ownerName !== '')
Halo {{ $ownerName }},
@else
Halo,
@endif

@if ($verified)
Petugas desa sudah memeriksa {{ $businessName }}. Kamu sekarang bisa menambah produk, mengisi kendala, dan melihat rekomendasi program.
@else
Petugas desa meminta perbaikan pada {{ $businessName }} sebelum usaha bisa diverifikasi.
@endif

@if ($note !== '')
Catatan petugas:
{{ $note }}

@endif
Buka tautan ini:
{{ $url }}
