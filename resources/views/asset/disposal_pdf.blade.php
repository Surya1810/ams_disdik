@php
// Decode payload JSON sebelum HTML agar variabel bisa digunakan di seluruh template
$payload = json_decode($disposal->payload, true);
$tanggal = \Carbon\Carbon::parse($disposal->created_at)->translatedFormat('d F Y');
$hari = \Carbon\Carbon::parse($disposal->created_at)->translatedFormat('l');
$jenis = $payload['jenis'] ?? null;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>
        @if($jenis === 'lelang')
        Berita Acara Lelang Barang
        @else
        Berita Acara Disposal Aset
        @endif
    </title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 40px;
            line-height: 1.5;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 20px;
        }

        .mt-4 {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        td,
        th {
            padding: 5px;
            vertical-align: top;
        }

        .signature {
            margin-top: 50px;
            width: 100%;
        }

        .signature td {
            text-align: center;
            height: 100px;
        }
    </style>
</head>

<body>

    <h2 class="center">
        @if($jenis === 'lelang')
        BERITA ACARA LELANG BARANG
        @else
        BERITA ACARA DISPOSAL ASET
        @endif
    </h2>

    <p class="center">
        <em>Nomor: {{ $disposal->id }}/BA/{{ strtoupper($jenis ?? 'DISPOSAL') }}/{{
            \Carbon\Carbon::parse($disposal->created_at)->format('Y') }}</em>
    </p>

    <p>
        Pada hari {{ $hari }} tanggal {{ $tanggal }}, telah dilakukan
        @if($jenis === 'lelang')
        kegiatan lelang terhadap aset milik instansi sebagaimana rincian berikut:
        @else
        proses pemusnahan atau penghapusan aset sebagai berikut:
        @endif
    </p>

    <table>
        <tr>
            <td width="30%">Nama Aset</td>
            <td width="5%">:</td>
            <td>{{ $disposal->asset->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kode Aset</td>
            <td>:</td>
            <td>{{ $disposal->asset->kode ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tanggal }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td>{{ $payload['keterangan'] ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis</td>
            <td>:</td>
            <td>{{ ucfirst($jenis ?? '-') }}</td>
        </tr>
    </table>

    <p class="mt-2">
        Demikian berita acara ini dibuat dengan sebenarnya untuk dapat digunakan sebagaimana mestinya.
    </p>

    <table class="signature">
        <tr>
            <td>Pihak yang Melaporkan,</td>
            <td>Penanggung Jawab,</td>
        </tr>
        <tr>
            <td><br><br><br><br><strong>{{ $disposal->user->name ?? '........' }}</strong></td>
            <td><br><br><br><br><strong>{{ $disposal->asset->location->name ?? '........' }}</strong></td>
        </tr>
    </table>

</body>

</html>
