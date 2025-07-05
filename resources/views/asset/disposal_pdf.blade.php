@php
$payload = json_decode($disposal->payload, true);
$createdAt = \Carbon\Carbon::parse($disposal->created_at);
$tanggal = $createdAt->translatedFormat('d F Y');
$hari = $createdAt->translatedFormat('l');
$jenis = $payload['jenis'] ?? 'disposal';
$nomor = $disposal->id . '/BA/' . strtoupper($jenis) . '/' . $createdAt->format('Y');
$asset = $disposal->asset;
$userName = $disposal->user->name ?? '........';
$penanggungJawab = $asset->location->name ?? '........';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara {{ ucfirst($jenis) }}</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 40px;
            line-height: 1.5;
        }

        h2 {
            text-align: center;
            margin: 0;
        }

        .center {
            text-align: center;
        }

        .mt-2 {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        td {
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

    <h2>
        @if($jenis === 'lelang')
        BERITA ACARA LELANG BARANG
        @else
        BERITA ACARA DISPOSAL ASET
        @endif
    </h2>

    <p class="center"><em>Nomor: {{ $nomor }}</em></p>

    <p>
        Pada hari {{ $hari }} tanggal {{ $tanggal }}, telah diajukan permohonan
        @if($jenis === 'lelang')
        kegiatan lelang terhadap aset milik instansi sebagaimana rincian berikut:
        @else
        proses pemusnahan atau penghapusan aset sebagai berikut:
        @endif
    </p>

    <table>
        <tr>
            <td width="30%">RFID Number</td>
            <td width="5%">:</td>
            <td>{{ $asset->rfid_number ?? '-' }}</td>
        </tr>
        <tr>
            <td width="30%">Nama Aset</td>
            <td width="5%">:</td>
            <td>{{ $asset->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kode Aset</td>
            <td>:</td>
            <td>{{ $asset->kode ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ $tanggal }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td>
                {{ $payload['keterangan'] ?? '-' }}
                <hr/>
                {{ $asset['merk'] ?? '-' }}
                <br/>
                {{ $asset['detail'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Jenis</td>
            <td>:</td>
            <td>{{ ucfirst($jenis) }}</td>
        </tr>
    </table>

    <p class="mt-2">
        Demikian berita acara ini dibuat dengan sebenarnya dan penuh tanggung jawab,
        untuk dapat digunakan sebagaimana mestinya sebagai bukti telah dilaksanakannya
        proses {{ $jenis === 'lelang' ? 'lelang' : 'disposal' }} aset sesuai ketentuan yang berlaku.
    </p>

    <table class="signature">
        <tr>
            <td>Pihak yang Melaporkan,</td>
            <td>Penanggung Jawab,</td>
        </tr>
        <tr>
            <td><br><br><br><br><strong>{{ $userName }}</strong></td>
            <td><br><br><br><br><strong>{{ $penanggungJawab }}</strong></td>
        </tr>
    </table>

</body>

</html>
