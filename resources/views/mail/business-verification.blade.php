<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $verified ? 'Usaha diverifikasi' : 'Usaha perlu perbaikan' }}</title>
</head>
<body style="margin:0;padding:0;background:#f0fdfa;color:#0f172a;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdfa;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border:1px solid #ccfbf1;border-radius:24px;">
                    <tr>
                        <td style="padding:28px 32px 20px;border-bottom:4px solid #0f766e;">
                            <p style="margin:0;font-size:18px;font-weight:800;letter-spacing:-0.02em;">Tumbuh<span style="color:#0f766e;">UMKM</span></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 32px;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;font-weight:800;">
                                {{ $verified ? 'Data usaha sudah diverifikasi' : 'Data usaha perlu perbaikan' }}
                            </h1>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#334155;">
                                @if ($ownerName !== '')
                                    Halo {{ $ownerName }},
                                @else
                                    Halo,
                                @endif
                            </p>
                            @if ($verified)
                                <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                                    Petugas desa sudah memeriksa {{ $businessName }}. Kamu sekarang bisa menambah produk, mengisi kendala, dan melihat rekomendasi program.
                                </p>
                            @else
                                <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                                    Petugas desa meminta perbaikan pada {{ $businessName }} sebelum usaha bisa diverifikasi.
                                </p>
                            @endif
                            @if ($note !== '')
                                <p style="margin:0 0 24px;padding:14px 16px;background:#f0fdfa;border-radius:16px;font-size:14px;line-height:1.6;color:#134e4a;">
                                    {{ $note }}
                                </p>
                            @endif
                            <a href="{{ $url }}" style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;line-height:1;padding:14px 22px;border-radius:999px;">
                                {{ $verified ? 'Buka dashboard' : 'Perbaiki profil usaha' }}
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
