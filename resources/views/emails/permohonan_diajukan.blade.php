<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Notifikasi Pengajuan {{ $jenis }}</title>
</head>

<body>
    <p>Yth. {{ $boss }},</p>

    <p>Pengajuan {{ $jenis }} telah dilakukan oleh <strong>{{ $user }}</strong> pada tanggal
        <strong>{{ $tanggalMulai }}</strong> hingga tanggal <strong>{{ $tanggalSelesai }}</strong>.
    </p>

    <p>Silahkan segera tinjau pengajuan tersebut.</p>

    @php
        $url = match ($jenis) {
            'Cuti Biasa', 'Cuti Spesial' => 'https://hris-infico.test/persetujuan-cuti',
            'Izin Satu Hari', 'Izin Setengah Hari Pagi', 'Izin Setengah Hari Siang' => 'https://hris-infico.test/izin',
            'Sakit' => 'https://hris-infico.test/sakit',
            'Permintaan Lembur' => 'https://hris-infico.test/permintaan-lembur',
            default => 'https://hris-infico.test',
        };
    @endphp

    <p>
        <a href="{{ $url }}"
            style="display: inline-block; padding: 10px 20px; background-color: #1D4ED8; color: #fff; text-decoration: none; border-radius: 5px;">
            Tinjau Pengajuan
        </a>
    </p>

    <br>
    <p>Terima kasih,<br>HRIS System</p>
</body>

</html>
