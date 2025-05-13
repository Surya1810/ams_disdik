<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $status == 'found' ? 'Berita Acara Penemuan' : 'Berita Acara Kehilangan' }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        th.group-header {
            background-color: #4F81BD;
            color: white;
            font-weight: bold;
        }

        th.sub-header {
            background-color: #4F81BD;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>{{ $status == 'found' ? 'BERITA ACARA PENEMUAN ASET' : 'BERITA ACARA KEHILANGAN ASET' }}</h2>

    <table>
        <thead>
            <tr>
                <th colspan="7" class="group-header">Informasi Barang</th>
                <th colspan="5" class="group-header">Lokasi</th>
            </tr>
            <tr>
                <th class="sub-header">RFID</th>
                <th class="sub-header">Kode Barang</th>
                <th class="sub-header">Nama/Jenis Barang</th>
                <th class="sub-header">Merk/Type</th>
                <th class="sub-header">Tahun Pembelian</th>
                <th class="sub-header">Kondisi</th>
                <th class="sub-header">Pinjaman</th>
                <th class="sub-header">Sekolah</th>
                <th class="sub-header">Ruangan</th>
                <th class="sub-header">Baris</th>
                <th class="sub-header">Rak</th>
                <th class="sub-header">Box</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $asset)
            <tr>
                <td>{{ $asset->rfid_number }}</td>
                <td>{{ $asset->kode }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->merk }}</td>
                <td>{{ $asset->tahun_pembelian }}</td>
                <td>{{ $asset->kondisi }}</td>
                <td>{{ $asset->pinjaman }}</td>
                <td>{{ $asset->sekolah->name ?? '-' }}</td>
                <td>{{ $asset->room }}</td>
                <td>{{ $asset->row }}</td>
                <td>{{ $asset->rack }}</td>
                <td>{{ $asset->box }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 40px; border: none;">
        <tr>
            <td style="text-align: right; border: none;">
                <p>{{ now()->format('d F Y') }}</p>
                <p style="margin-top: 60px;">.............................................</p>
                <p>Petugas</p>
            </td>
        </tr>
    </table>

</body>

</html>