<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $status == 'found' ? 'Berita Acara Penemuan Aset' : 'Berita Acara Kehilangan Aset' }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            margin: 40px;
            color: #000;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 18px;
        }

        p.intro {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        th.group-header {
            background-color: #2F5496;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }

        th.sub-header {
            background-color: #4F81BD;
            color: #fff;
            font-weight: bold;
        }

        /* Signature section */
        .signature {
            width: 100%;
            margin-top: 60px;
            border: none;
        }

        .signature td {
            border: none;
            text-align: right;
            padding-right: 40px;
            font-size: 14px;
        }

        .signature .name-line {
            margin-top: 80px;
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 220px;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>{{ $status == 'found' ? 'BERITA ACARA PENEMUAN ASET' : 'BERITA ACARA KEHILANGAN ASET' }}</h2>

    <p class="intro">
        Pada hari ini, tanggal {{ \Carbon\Carbon::parse($scan->created_at)->translatedFormat('d F Y') }}, telah
        dilakukan kegiatan pemindaian aset di lokasi
        <strong>{{ $scan->place_name ?? '-' }}</strong>, Kecamatan <strong>{{ $scan->district_name ?? '-' }}</strong>.
        Berdasarkan hasil kegiatan tersebut, ditemukan <strong>{{ $status == 'found' ? 'aset yang berhasil ditemukan' :
            'aset yang hilang' }}</strong> sebagai berikut:
    </p>

    <table>
        <thead>
            <tr>
                <th colspan="7" class="group-header">Informasi Barang</th>
                <th colspan="2" class="group-header">Lokasi</th>
            </tr>
            <tr>
                <th class="sub-header">RFID</th>
                <th class="sub-header">Kode Barang</th>
                <th class="sub-header">Nama/Jenis Barang</th>
                <th class="sub-header">Merk/Type</th>
                <th class="sub-header">Kondisi</th>
                <th class="sub-header">Gedung</th>
                <th class="sub-header">Lantai</th>
                <th class="sub-header">Ruangan</th>
                <th class="sub-header">Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $asset)
            <tr>
                <td>{{ $asset->rfid_number }}</td>
                <td>{{ $asset->kode }}</td>
                <td style="text-align: left; padding-left: 8px;">{{ $asset->name }}</td>
                <td style="text-align: left; padding-left: 8px;">{{ $asset->merk }}</td>
                <td>{{ $asset->kondisi }}</td>
                <td>{{ $asset->gedung }}</td>
                <td>{{ $asset->lantai }}</td>
                <td>{{ $asset->ruangan }}</td>
                <td style="text-align: left; padding-left: 8px;">{{ $asset->detail }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signature">
        <tr>
            <td>
                <p>{{ now()->translatedFormat('d F Y') }}</p>
                <p class="name-line">Petugas</p>
            </td>
        </tr>
    </table>

</body>

</html>