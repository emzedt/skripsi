<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $penggajian->user->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,
        h2 {
            text-align: center;
        }

        .logo-container {
            text-align: left;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 150px;
            height: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .highlight {
            background-color: yellow;
        }

        .total {
            font-weight: bold;
            border: none;
        }

        .section-title {
            margin-top: 20px;
        }

        .uang-harian-details,
        .uang-lembur-details {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .header-container {
            display: flex;
            align-items: center;
        }

        .header-container img {
            height: 60px;
            margin-right: 20px;
        }

        .header-title {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <img src="{{ public_path('logo.png') }}" alt="Logo Perusahaan">
        <div class="header-title">
            SLIP GAJI
        </div>
    </div>

    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr>
                <td style="border: none; padding: 1px 8px 1px 0; width: 140px;">Nama</td>
                <td style="border: none; padding: 1px 8px; width: 10px;">:</td>
                <td style="border: none; padding: 1px 0;">{{ $penggajian->user->nama ?? 'Rizky Ananda Geovani' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 1px 8px 1px 0;">Jabatan</td>
                <td style="border: none; padding: 1px 8px;">:</td>
                <td style="border: none; padding: 1px 0;">{{ $penggajian->user->jabatan->nama ?? 'Helper' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 1px 8px 1px 0;">Periode</td>
                <td style="border: none; padding: 1px 8px;">:</td>
                <td style="border: none; padding: 1px 0;">
                    {{ $penggajian->periode_mulai->format('d F Y') ?? '27 Jan 2025' }} -
                    {{ $penggajian->periode_selesai->format('d F Y') ?? '2 Feb 2025' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 1px 8px 1px 0;">Tanggal Cetak</td>
                <td style="border: none; padding: 1px 8px;">:</td>
                <td style="border: none; padding: 1px 0;">
                    {{ $tanggalCetak ?? \Carbon\Carbon::now()->format('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 1px 8px 1px 0;">Status Karyawan</td>
                <td style="border: none; padding: 1px 8px;">:</td>
                <td style="border: none; padding: 1px 0;">
                    {{ $penggajian->user->isKaryawanTetap() ? 'Karyawan Tetap' : 'Karyawan Harian' ?? 'Karyawan Harian' }}
                </td>
            </tr>
        </tbody>
    </table>

    @if ($penggajian->user->isKaryawanTetap())
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td style="border: none;">Gaji Pokok</td>
                <td style="text-align: right; border: none;">Rp
                    {{ number_format($penggajian->user->gajiBulanan->gaji_bulanan ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none;">Potongan</td>
                <td style="text-align: right; border: none;">
                    Rp {{ number_format($penggajian->potongan_gaji, 0, ',', '.') }}
                </td>
            </tr>
        </table>
        </div>
    @else
        <div class="uang-harian-details">
            <h3>PENGHASILAN</h3>
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="border: none;">Upah Harian</td>
                    <td style="text-align: right; border: none;">
                        Rp {{ number_format($penggajian->user->gajiHarian->gaji_harian, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Proyek</th>
                        <th>Waktu Lembur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penggajian->user->absensi->whereBetween('tanggal', [$penggajian->periode_mulai, $penggajian->periode_selesai]) as $absensi)
                        @php
                            // Cari permintaan lembur yang sesuai dengan tanggal absensi
                            $lembur = $penggajian->user->permintaanLembur
                                ->where('tanggal_mulai', '<=', $absensi->tanggal)
                                ->where('tanggal_selesai', '>=', $absensi->tanggal)
                                ->first();
                        @endphp
                        <tr>
                            <td>{{ $absensi->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $absensi->lokasi->nama }}</td>
                            <td>
                                @if ($lembur)
                                    {{ $lembur->jam_mulai }} - {{ $lembur->jam_akhir }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Tidak ada detail kehadiran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="uang-lembur-details">
            <table style="border-collapse: collapse; width: 100%;">
                <tr>
                    <td style="border: none;">Uang Makan</td>
                    <td style="text-align: right; border: none;">
                        Rp {{ number_format($penggajian->user->gajiHarian->upah_makan_harian, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Uang Lembur</td>
                    <td style="text-align: right; border: none;">
                        Rp {{ number_format($penggajian->lembur, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Jumlah Lembur Over (> 5 Jam)</td>
                    <td style="text-align: right; border: none;">
                        {{ $overtimeOver5Hours }} x
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Total Jumlah Jam Lembur</td>
                    <td style="text-align: right; border: none;">
                        {{ $totalJamLembur }} Jam
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Potongan</td>
                    <td style="text-align: right; border: none;">
                        Rp {{ number_format($penggajian->potongan_gaji, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <table class="border-collapse: collapse; width: 100%;">
        <tr class="highlight" style="border: 1px solid #000; padding: 8px;">
            <td class="total">GAJI YANG DITERIMA</td>
            <td class="total" style="text-align: right">Rp
                {{ number_format($penggajian->gaji_diterima, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>

</html>
