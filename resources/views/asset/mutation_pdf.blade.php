<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pengajuan Mutasi Aset</title>
    <style>
        body {
        font-family: "Times New Roman", serif;
        font-size: 12pt;
        line-height: 1.6;
        margin: 25px;
        color: #222;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .subtitle {
            text-align: center;
            font-style: italic;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            width: 30%;
            font-weight: bold;
        }

        .separator {
            width: 5%;
        }

        .content {
            width: 65%;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mt-4 {
            margin-top: 40px;
        }

        .signature-table {
            width: 100%;
            margin-top: 20px;
        }

        .signature-table td {
        text-align: center;
        vertical-align: bottom;
        padding-top: 40px;
        }
    </style>
</head>

<body>

    @php
    $payload = json_decode($mutation->payload, true);
    $from = $payload['from'] ?? [];
    $to = $payload['to'] ?? [];
    @endphp

    <h2>Berita Acara Pengajuan Mutasi Aset</h2>
    <div class="subtitle">
        Nomor: {{ $mutation->id }}/BA-MUTASI/{{ \Carbon\Carbon::parse($mutation->created_at)->format('Y') }}
    </div>

    <p>Pada hari ini, <strong>{{ \Carbon\Carbon::parse($mutation->created_at)->translatedFormat('l') }}</strong>,
        tanggal <strong>{{ \Carbon\Carbon::parse($mutation->created_at)->translatedFormat('d F Y') }}</strong>, Dengan penuh tanggung jawab, yang bertanda tangan di bawah ini menyatakan telah mengajukan permohonan mutasi aset,
        sebagaimana rincian berikut:
    </p>

    <table>
        <tr>
            <td class="label">Nama Aset</td>
            <td class="separator">:</td>
            <td class="content">{{ $mutation->asset->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kode Aset</td>
            <td class="separator">:</td>
            <td class="content">{{ $mutation->asset->kode ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label" valign="top">PIC Awal</td>
            <td class="separator" valign="top">:</td>
            <td class="content">
                <strong>Nama:</strong> {{ $from['nama_pic'] ?? '-' }}<br>
                <strong>NIP:</strong> {{ $from['nip_pic'] ?? '-' }}<br>
                <strong>Jabatan:</strong> {{ $from['jabatan_pic'] ?? '-' }}<br>
                <strong>No. Telepon:</strong> {{ $from['telp_pic'] ?? '-' }}<br>
                <strong>Keterangan:</strong> {{ $from['keterangan'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <td class="label" valign="top">PIC Tujuan</td>
            <td class="separator" valign="top">:</td>
            <td class="content">
                <strong>Nama:</strong> {{ $to['nama_pic'] ?? '-' }}<br>
                <strong>NIP:</strong> {{ $to['nip_pic'] ?? '-' }}<br>
                <strong>Jabatan:</strong> {{ $to['jabatan_pic'] ?? '-' }}<br>
                <strong>No. Telepon:</strong> {{ $to['telp_pic'] ?? '-' }}<br>
                <strong>Keterangan:</strong> {{ $to['keterangan'] ?? '-' }}
            </td>
        </tr>
    </table>

    <p class="mt-2">
        Dengan ini, telah diajukan permohonan mutasi aset sebagaimana tercantum di atas dan menyatakan bahwa seluruh
        data yang diberikan adalah benar. Berita acara ini dibuat sebagai bukti pengajuan yang sah dan dapat dipergunakan sebagaimana mestinya untuk
        keperluan administrasi lebih lanjut.
    </p>

    <table class="signature-table">
        <tr>
            <td>Pengaju,</td>
            <td>Disetujui Oleh,</td>
        </tr>
        <tr>
            <td><br><br><br><br><strong>{{ $mutation->from->name ?? '........' }}</strong></td>
            <td><br><br><br><br><strong>{{ $mutation->to->name ?? '........' }}</strong></td>
        </tr>
    </table>

</body>

</html>