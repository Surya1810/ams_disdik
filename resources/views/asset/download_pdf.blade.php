<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Detail Aset</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .image-container {
            text-align: center;
            margin-top: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 8px;
            vertical-align: top;
        }

        .info-table th {
            text-align: left;
            padding: 8px;
            background-color: #f2f2f2;
            width: 25%;
        }

        .info-table,
        .info-table th,
        .info-table td {
            border: 1px solid #ddd;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .section-divider {
            margin: 20px 0;
            border-top: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Detail Aset</h2>
    </div>

    <div class="image-container">
        <h4>Foto Aset</h4>
        @if ($asset->foto_awal)
        <img src="{{ $asset->foto_awal }}" alt="Foto Awal Aset" width="300"/>
        @else
        <p><em>Gambar tidak tersedia</em></p>
        @endif
    </div>
    <div class="image-container">
        <h4>Foto Aset</h4>
        @if ($asset->foto_awal)
        <img src="{{ $asset->foto_kondisi }}" alt="Foto Kondisi Aset Terbaru" width="300"/>
        @else
        <p><em>Gambar tidak tersedia</em></p>
        @endif
    </div>

    <div class="section-title">Informasi Barang</div>
    <table class="info-table">
        <tr>
            <th>RFID Number</th>
            <td>{{ $asset->rfid_number }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $asset->name }}</td>
        </tr>
        <tr>
            <th>Kode barang</th>
            <td>{{ $asset->kode }}</td>
        </tr>
        <tr>
            <th>Nomor Register</th>
            <td>{{ $asset->register }}</td>
        </tr>
        <tr>
            <th>Merk</th>
            <td>{{ $asset->merk }}</td>
        </tr>
        <tr>
            <th>Ukuran</th>
            <td>{{ $asset->ukuran }}</td>
        </tr>
        <tr>
            <th>Bahan</th>
            <td>{{ $asset->bahan }}</td>
        </tr>
        <tr>
            <th>Tahun Pembelian</th>
            <td>{{ $asset->tahun_pembelian }}</td>
        </tr>
        <tr>
            <th>Pabrik</th>
            <td>{{ $asset->pabrik }}</td>
        </tr>
    </table>

    <div class="section-title">Nomor Barang</div>
    <table class="info-table">
        <tr>
            <th>Rangka</th>
            <td>{{ $asset->rangka }}</td>
        </tr>
        <tr>
            <th>Mesin</th>
            <td>{{ $asset->mesin }}</td>
        </tr>
        <tr>
            <th>Polisi</th>
            <td>{{ $asset->polisi }}</td>
        </tr>
        <tr>
            <th>BPKB</th>
            <td>{{ $asset->bpkb }}</td>
        </tr>
    </table>

    <div class="section-title">PIC</div>
    <table class="info-table">
        <tr>
            <th>Nama PIC</th>
            <td>{{ $asset->nama_pic }} ({{ $asset->nip_pic }} - {{ $asset->jabatan_pic }})</td>
        </tr>
        <tr>
            <th>No. Telepon PIC</th>
            <td>{{ $asset->telp_pic }}</td>
        </tr>
    </table>

    <div class="section-title">Perawatan Barang</div>
    <table class="info-table">
        <tr>
            <th>Asal Perolehan</th>
            <td>{{ $asset->asal_perolehan }}</td>
        </tr>
        <tr>
            <th>Nilai Perolehan</th>
            <td>Rp{{ number_format($asset->nilai_perolehan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Kondisi</th>
            <td>{{ $asset->kondisi }}</td>
        </tr>
        <tr>
            <th>Tanggal Perawatan</th>
            <td>{{ $asset->tanggal_perawatan }}</td>
        </tr>
        <tr>
            <th>Harga Perawatan</th>
            <td>Rp{{ number_format($asset->harga_perawatan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Jangka Waktu Perawatan</th>
            <td>{{ $asset->waktu_perawatan }} bulan</td>
        </tr>
    </table>

    <div class="section-title">Lokasi Barang</div>
    <table class="info-table">
        <tr>
            <th>Kecamatan</th>
            <td>{{ $places->kecamatan->name }}</td>
        </tr>
        <tr>
            <th>Tempat</th>
            <td>{{ $places->category . ' ' . $places->name }}</td>
        </tr>
        <tr>
            <th>Lokasi</th>
            <td>Gedung: {{ $asset->gedung }}, Lantai: {{ $asset->lantai }}, Ruangan: {{ $asset->ruangan }}</td>
        </tr>
        <tr>
            <th>Detail</th>
            <td>{{ $asset->detail }}</td>
        </tr>
    </table>
    </table>

</body>

</html>
