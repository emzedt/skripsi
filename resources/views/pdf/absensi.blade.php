<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .period {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Absensi</h1>
        @if ($startDate || $endDate)
            <div class="period">
                <p>
                    Periode:
                    {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} -
                    {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}
                </p>
            </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
                <th>Lokasi Masuk</th>
                <th>Lokasi Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->jam_masuk }}</td>
                    <td>{{ $item->jam_keluar }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        @if ($item->latitude_masuk && $item->longitude_masuk)
                            <a href="https://www.google.com/maps?q={{ $item->latitude_masuk }},{{ $item->longitude_masuk }}"
                                target="_blank">
                                Lihat Lokasi
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($item->latitude_keluar && $item->longitude_keluar)
                            <a href="https://www.google.com/maps?q={{ $item->latitude_keluar }},{{ $item->longitude_keluar }}"
                                target="_blank">
                                Lihat Lokasi
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
