<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Peminjaman Aset</title>
    <style>
        body {
        font-family: "Times New Roman", serif;
        font-size: 12pt;
        line-height: 1.6;
        margin: 25px;
        color: #222;
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
            margin-top: 10px;
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
            margin-top: 20px;
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
    $payload = json_decode($loan->payload, true);
    $from = $payload['old_values'] ?? [];
    $to = $payload['new_values'] ?? [];
    $sekolah = \App\Models\Sekolah::find($from['sekolah_id'] ?? $to['sekolah_id'] ?? null);
    $sekolahName = $sekolah->name ?? '-';
    @endphp

    <h2>BERITA ACARA PEMINJAMAN ASET</h2>
    <p class="center"><em>Nomor: {{ $loan->id }}/BA-PINJAM/{{ \Carbon\Carbon::parse($loan->created_at)->format('Y')
            }}</em></p>

    <p>Pada hari ini, {{ \Carbon\Carbon::parse($loan->created_at)->translatedFormat('l') }} tanggal
        {{ \Carbon\Carbon::parse($loan->created_at)->translatedFormat('d F Y') }}, telah diajukan permohonan peminjaman
        aset dengan rincian sebagai berikut:</p>

    <table>
        <tr>
            <td width="30%">Nama Aset</td>
            <td width="5%">:</td>
            <td>{{ $loan->asset->name }}</td>
        </tr>
        <tr>
            <td>Kode Aset</td>
            <td>:</td>
            <td>{{ $loan->asset->kode }}</td>
        </tr>
        <tr>
            <td valign="top">Lokasi Awal</td>
            <td valign="top">:</td>
            <td>
                <strong>Sekolah:</strong> {{ $sekolahName }}<br>
                <strong>Gedung:</strong> {{ $from['gedung'] ?? '-' }}<br>
                <strong>Lantai:</strong> {{ $from['lantai'] ?? '-' }}<br>
                <strong>Ruangan:</strong> {{ $from['ruangan'] ?? '-' }}<br>
                <strong>Detail:</strong> {{ $from['detail'] ?? '-' }}<br>
            </td>
        </tr>
        <tr>
            <td valign="top">Lokasi Tujuan (Peminjam)</td>
            <td valign="top">:</td>
            <td>
                <strong>Sekolah:</strong> {{ $sekolahName }}<br>
                <strong>Gedung:</strong> {{ $to['gedung'] ?? '-' }}<br>
                <strong>Lantai:</strong> {{ $to['lantai'] ?? '-' }}<br>
                <strong>Ruangan:</strong> {{ $to['ruangan'] ?? '-' }}<br>
                <strong>Detail:</strong> {{ $to['detail'] ?? '-' }}<br>
            </td>
        </tr>
        <tr>
            <td>Tanggal Peminjaman</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($loan->created_at)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td>{{ $payload['keterangan'] ?? '-' }}</td>
        </tr>
    </table>

    <p class="mt-2">Sebagai pihak yang bertanggung jawab, kami mengajukan permohonan peminjaman aset ini dengan penuh
        keseriusan untuk mengikuti semua prosedur yang berlaku. Berita acara ini dibuat dan ditandatangani oleh pihak
        yang terkait untuk dapat dipergunakan sebagaimana mestinya.</p>

    <table class="signature">
        <tr>
            <td>Peminjam,</td>
            <td>Penanggung Jawab Aset,</td>
        </tr>
        <tr>
            <td><br><br><br><br><strong>{{ $loan->to->name ?? '........' }}</strong></td>
            <td><br><br><br><br><strong>{{ $loan->from->name ?? '........' }}</strong></td>
        </tr>
    </table>

</body>

</html>
