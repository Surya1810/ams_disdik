<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Mutasi Aset</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 40px;
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

    @php
    $payload = json_decode($mutation->payload, true);
    $from = $payload['from'] ?? [];
    $to = $payload['to'] ?? [];
    @endphp
    
    <h2>BERITA ACARA PERPINDAHAN</h2>
    <p class="center">
        <em>Nomor: {{ $mutation->id }}/BA-MUTASI/{{ \Carbon\Carbon::parse($mutation->created_at)->format('Y') }}</em>
    </p>
    
    <p>Pada hari ini, {{ \Carbon\Carbon::parse($mutation->created_at)->translatedFormat('l') }} tanggal
        {{ \Carbon\Carbon::parse($mutation->created_at)->translatedFormat('d F Y') }}, telah dilakukan proses mutasi
        aset sebagai berikut:</p>
    
    <table>
        <tr>
            <td width="30%">Nama Aset</td>
            <td width="5%">:</td>
            <td>{{ $mutation->asset->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kode Aset</td>
            <td>:</td>
            <td>{{ $mutation->asset->kode ?? '-' }}</td>
        </tr>
        <tr>
            <td valign="top">PIC Awal</td>
            <td valign="top">:</td>
            <td>
                <strong>Nama PIC:</strong> {{ $from['nama_pic'] ?? '-' }}<br>
                <strong>NIP PIC:</strong> {{ $from['nip_pic'] ?? '-' }}<br>
                <strong>Jabatan PIC:</strong> {{ $from['jabatan_pic'] ?? '-' }}<br>
                <strong>No.Telepon PIC:</strong> {{ $from['telp_pic'] ?? '-' }}<br>
                <strong>Keterangan:</strong> {{ $from['keterangan'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <td valign="top">PIC Tujuan</td>
            <td valign="top">:</td>
            <td>
                <strong>Nama PIC:</strong> {{ $to['nama_pic'] ?? '-' }}<br>
                <strong>NIP PIC:</strong> {{ $to['nip_pic'] ?? '-' }}<br>
                <strong>Jabatan PIC:</strong> {{ $to['jabatan_pic'] ?? '-' }}<br>
                <strong>No.Telepon PIC:</strong> {{ $to['telp_pic'] ?? '-' }}<br>
                <strong>Keterangan:</strong> {{ $to['keterangan'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Tanggal Mutasi</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($mutation->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>
    <p class="mt-2">Demikian berita acara ini dibuat dengan sebenarnya untuk dapat digunakan sebagaimana mestinya.</p>

    <table class="signature">
        <tr>
            <td>Dibuat Oleh,</td>
            <td>Disetujui Oleh,</td>
        </tr>
        <tr>
            <td><br><br><br><br><strong>{{ $mutation->from->name ?? '........' }}</strong></td>
            <td><br><br><br><br><strong>{{ $mutation->to->name ?? '........' }}</strong></td>
        </tr>
    </table>

</body>

</html>