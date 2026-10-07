<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atur ulang kata sandi</title>
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
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;font-weight:800;">Atur ulang kata sandi</h1>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#334155;">
                                @if ($name !== '')
                                    Halo {{ $name }},
                                @else
                                    Halo,
                                @endif
                            </p>
                            <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#334155;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Tumbuh UMKM. Tombol di bawah membuka halaman untuk membuat kata sandi baru.
                            </p>
                            <a href="{{ $url }}" style="display:inline-block;background:#0f766e;color:#ffffff;text-decoration:none;font-weight:700;font-size:15px;line-height:1;padding:14px 22px;border-radius:999px;">Atur ulang kata sandi</a>
                            <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#64748b;">
                                Tautan ini berlaku {{ $expireMinutes }} menit. Jika kamu tidak meminta pengaturan ulang, abaikan email ini. Kata sandi tidak berubah sebelum tautan dibuka.
                            </p>
                            <p style="margin:16px 0 0;font-size:12px;line-height:1.6;color:#64748b;">
                                Jika tombol tidak terbuka, salin tautan berikut ke peramban:
                            </p>
                            <p style="margin:8px 0 0;font-size:12px;line-height:1.6;color:#0f766e;word-break:break-all;">{{ $url }}</p>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-size:12px;line-height:1.5;color:#64748b;">Tumbuh UMKM · data usaha dan pembinaan desa</p>
            </td>
        </tr>
    </table>
</body>
</html>
