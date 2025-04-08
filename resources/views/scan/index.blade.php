@extends('layouts.app')

@section('title', 'Scan')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Scan</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Scan</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <!-- Tabel Asset -->
            <div class="col-12 col-md-6">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="text-white text-capitalize ps-3">Asset Terdaftar</h6>
                                </div>
                                <div class="col-6 text-end ps-3 text-white pe-4">
                                    <small>Found: <strong id="totalIsThereTrue">0</strong></small>
                                    <small>Missing: <strong id="totalIsThereFalse">0</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <table id="assetTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Nama Sekolah</th>
                                    <th class="text-uppercase">Kategori</th>
                                    <th class="text-uppercase">Kecamatan</th>
                                    <th class="text-uppercase">Jumlah Aset</th>
                                    <th class="text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data Asset -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Tag -->
            <div class="col-12 col-md-6">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="text-white text-capitalize ps-3">Scanned Data</h6>
                                </div>
                                <div class="col-6 text-end ps-3 text-white pe-4">
                                    <small class="float-right">Total :<strong id="totalRFID">0</strong>
                                        last checked: <strong>
                                            @isset($last_checked->created_at)
                                                {{ $last_checked->created_at }}
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
                                    <th class="text-uppercase">RFID</th>
                                    <th class="text-uppercase">Timestamp</th>
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
        let rfidTable = $('#rfidTable').DataTable({
            "paging": true,
            "processing": true,
            "lengthChange": true,
            "searching": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "ordering": true,
            "serverSide": false,
            "destroy": true,
            "oLanguage": {
                "sEmptyTable": "Waiting scanner data"
            },
            columns: [{
                    data: 'rfid_number'
                },
                {
                    data: 'timestamp'
                }
            ]
        });

        let assetTable = $('#assetTable').DataTable({
            "paging": true,
            "processing": true,
            "lengthChange": true,
            "searching": true,
            "info": true,
            "autoWidth": false,
            "responsive": false,
            "ordering": false,
            "scrollX": true,
            "serverSide": false,
            "destroy": true,
        });

        // Variabel untuk menyimpan jumlah total
        let totalRFID = 0;
        let totalIsThereTrue = 0;
        let totalIsThereFalse = 0;

        // Fungsi untuk mengambil data RFID dari cache
        function loadCachedData() {
            $.getJSON('/api/scan-asset', function(response) {
                if (response && response.data && response.data.tags) {
                    totalRFID = response.data.total;
                    updateRFIDTable(response.data.tags);
                    updateTotalDisplay();
                }
            });
        }


        // Fungsi untuk memperbarui tabel asset dari API
        function updateAssetTable(callback = null) {
            $.getJSON('/api/assets', function(data) {
                assetTable.clear();

                totalIsThereTrue = 0;
                totalIsThereFalse = 0;

                data.forEach((asset) => {
                    let rowNode = assetTable.row.add([
                        asset.is_there ? ' <strong>FOUND</strong>' : '<strong>MISSING</strong>',
                        asset.rfid_number,
                        asset.cif,
                        asset.nik_nasabah,
                        asset.rekening_nasabah,
                        asset.nama_nasabah,
                        asset.cabang,
                        asset.no_dokumen,
                        asset.segmen,
                        asset.pinjaman,
                        asset.room,
                        asset.row,
                        asset.rack,
                        asset.box
                    ]).node();

                    if (asset.is_there) {
                        totalIsThereTrue++;
                        $(rowNode).addClass('bg-success-2');
                    } else {
                        totalIsThereFalse++;
                        $(rowNode).addClass('bg-danger-2');
                    }
                });

                assetTable.draw();
                updateTotalDisplay();
                if (callback) callback();
            });
        }

        // Fungsi untuk memperbarui tabel RFID
        function updateRFIDTable(data) {
            rfidTable.clear();
            data.forEach((rfid) => {
                rfidTable.row.add({
                    rfid_number: rfid,
                    timestamp: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    })
                });
            });
            rfidTable.draw();
        }

        // Fungsi untuk update tampilan total
        function updateTotalDisplay() {
            $('#totalRFID').text(totalRFID);
            $('#totalIsThereTrue').text(totalIsThereTrue);
            $('#totalIsThereFalse').text(totalIsThereFalse);
        }

        // Tambahkan event listener ke tombol refresh manual
        $('#refreshButton').on('click', function() {
            loadCachedData();
            updateAssetTable();
        });

        // Panggil pertama kali saat halaman dimuat
        loadCachedData();
        updateAssetTable();
    </script>
@endpush
