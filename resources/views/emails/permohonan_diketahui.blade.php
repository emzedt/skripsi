<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Status Permohonan {{ $jenis }}</title>
</head>

<body>
    <h2>Yth. {{ $boss->nama }}</h2>

    <p>
        Permohonan pengajuan {{ $jenis }} {{ $user->nama }} telah <strong>{{ $status }}</strong>.
    </p>

    @if ($alasan)
        <p><strong>Catatan dari atasan:</strong><br>{{ $alasan }} oleh {{ optional($user->boss())->nama }}</p>
    @endif

    <br>
    <p>Hormat kami,<br>HRIS System</p>
</body>

</html>
