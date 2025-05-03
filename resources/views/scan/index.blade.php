@extends('layouts.app')

@section('title', 'History Stock Opname')

@push('css')
    <style>
        #filterStatus:hover {
            cursor: pointer;
        }
    </style>
@endpush

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">History Stock Opname</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">History Stock Opname</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12 mb-3 text-center">
            <button class="btn btn-primary rounded-partner" id="refreshButton"><i class="fa-solid fa-arrows-rotate"></i>
                Refresh Data</button>
        </div>
    </div>
    <div>
        <!-- Tabel Asset -->
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h6 class="text-white text-capitalize ps-3">Asset Terdaftar</h6>
                            </div>
                            <div class="col-6 text-end ps-3 text-white pe-4">
                                <small>Found: <strong id="totalIsThereTrue">{{ $status['foundCount'] }}</strong></small>
                                <small>Missing: <strong id="totalIsThereFalse">{{ $status['missingCount'] }}</strong></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <div class="col-md-2">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white border-0">
                                <i class="fa-solid fa-filter"></i>
                            </span>
                            <select id="filterStatus" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1">FOUND</option>
                                <option value="0">MISSING</option>
                            </select>
                        </div>
                    </div>
                    <div class="p-2 mb-3 overflow-x-scroll">
                        <table id="assetTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-uppercase">RFID</th>
                                    <th class="text-uppercase">Kode Barang</th>
                                    <th class="text-uppercase">Nama/Jenis Barang</th>
                                    <th class="text-uppercase">Merk/Type</th>
                                    <th class="text-uppercase">Tahun Pembelian</th>
                                    <th class="text-uppercase">Kondisi</th>
                                    <th class="text-uppercase">Tempat</th>
                                    <th class="text-uppercase">Gedung</th>
                                    <th class="text-uppercase">Lantai</th>
                                    <th class="text-uppercase">Ruangan</th>
                                    <th class="text-uppercase">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data Asset -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12 mb-3 text-center">
                    <button class="btn btn-primary rounded-partner" id="exportFound">
                        <i class="fa-solid fa-download"></i> Export Found
                    </button>
                    <button class="btn btn-primary rounded-partner" id="exportMissing">
                        <i class="fa-solid fa-download"></i> Export Missing
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Tag -->
        <div class="col-12 mt-5 pt-3">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h6 class="text-white text-capitalize ps-3">Scanned Data</h6>
                            </div>
                            <div class="col-6 text-end ps-3 text-white pe-4">
                                <small class="float-right">Total : <strong id="totalRFID">{{ $scansCount }}</strong>
                                    last checked: <strong>
                                        @isset($lastScan->created_at)
                                        {{ $lastScan->created_at }}
                                        @endisset
                                    </strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <table id="rfidTable" class="table text-sm mt-3">
                        <thead class="font-weight-bolder">
                            <tr>
                                <th class="text-uppercase">Total Discan</th>
                                <th class="text-uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data RFID -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type='text/javascript'>
    const rfidTable = $('#rfidTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('scanned.assets') }}',
        },
        columns: [
            {
                data: 'total',
                name: 'total',
                className: 'text-start'
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: "text-start"
            }
        ],
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
        }
    });

    const table = $('#assetTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('scan.index') }}',
            data: function (d) {
                d.is_there = $('#filterStatus').find(':selected').val();
            }
        },
        rowCallback: function(row, data, index) {
            // Cek isi kolom 'is_there' (karena pakai raw HTML <strong>)
            const isFound = data.is_there.includes('FOUND');

            if (isFound) {
                $(row).removeClass('table-danger').addClass('table-success');
            } else {
                $(row).removeClass('table-success').addClass('table-danger');
            }
        },
        columns: [
            {
                data: 'is_there',
                name: 'is_there',
                className: 'text-start'
            },
            {
                data: 'rfid_number',
                name: 'rfid_number',
                className: "text-start"
            },
            {
                data: 'kode',
                name: 'kode',
                className: "text-start"
            },
            {
                data: 'name',
                name: 'name',
                className: "text-start"
            },
            {
                data: 'merk',
                name: 'merk',
                className: "text-start"
            },
            {
                data: 'tahun_pembelian',
                name: 'tahun_pembelian',
                className: "text-start"
            },
            {
                data: 'kondisi',
                name: 'kondisi',
                className: "text-start"
            },
            {
                data: 'sekolah.name',
                name: 'sekolah.name',
                className: 'text-start'
            },
            {
                data: 'gedung',
                name: 'gedung',
                className: 'text-start'
            },
            {
                data: 'lantai',
                name: 'lantai',
                className: 'text-start'
            },
            {
                data: 'ruangan',
                name: 'ruangan',
                className: 'text-start'
            },
            {
                data: 'detail',
                name: 'detail',
                className: 'text-start'
            }
        ],
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });
        }
    });


    // Tombol untuk mengekspor data Found
    $('#exportFound').on('click', function () {
        window.location.href = '/scan/export/found';
    });

    // Tombol untuk mengekspor data Missing
    $('#exportMissing').on('click', function () {
        window.location.href = '/scan/export/missing';
    });

    // Tambahkan event listener ke tombol refresh manual
    $('#refreshButton').on('click', function() {
        table.ajax.reload();
        rfidTable.ajax.reload();
    });

    $('#filterStatus').on('change', function () {
        table.ajax.reload();
    });
</script>
@endpush
