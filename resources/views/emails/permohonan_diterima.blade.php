<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Status Permohonan {{ $jenis }}</title>
</head>

<body>
    <h2>Halo, {{ $user->nama }}</h2>

    <p>
        Permohonan {{ $jenis }} Anda telah <strong>{{ $status }}</strong>.
    </p>

    @if ($alasan)
        <p><strong>Catatan dari atasan:</strong><br>{{ $alasan }}</p>
    @endif

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
    <p>Hormat kami,<br>HRIS System</p>
</body>

</html>
