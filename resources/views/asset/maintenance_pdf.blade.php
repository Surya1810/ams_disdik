<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Laporan Maintenance Aset</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 14px;
            margin: 40px;
            color: #000;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .print-date {
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px 12px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        tfoot td[colspan="4"] {
            text-align: right;
        }
    </style>
</head>

<body>
    <h3>Laporan Maintenance Aset</h3>
    <div class="print-date">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Kondisi</th>
                <th>Tanggal Perawatan</th>
                <th>Waktu Perawatan</th>
                <th>Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($maintenanceList as $item)
            <tr>
                <td>{{ $item->name ?? '-' }}</td>
                <td>{{ $item->kondisi ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_perawatan)->translatedFormat('d F Y') }}</td>
                <td>{{ $item->waktu_perawatan ? $item->waktu_perawatan . ' Bulan' : '-' }}</td>
                <td style="text-align: right;">{{ number_format($item->harga_perawatan ?? 0, 0, ',', '.') }}</td>
            </tr>
            @php $total += $item->harga_perawatan ?? 0; @endphp
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Tidak ada data maintenance.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td style="text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>