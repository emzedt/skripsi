<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi Sales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        <h1>Laporan Absensi Sales</h1>
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
                <th>Nama Sales</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Deskripsi</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($absensiSales as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->nama }}</td>
                    <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $item->created_at->format('H:i') }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td class="photo-cell">
                        @if ($item->foto)
                            <img src="{{ public_path('storage/absensi_sales/' . $item->foto) }}" alt="Foto Absensi"
                                width="100">
                        @else
                            Tidak ada foto
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
