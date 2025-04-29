@extends('layouts.app')

@section('title', 'Maintenance')

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Maintenance</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Maintenance</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">

                    <div
                        class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h6 class="text-white text-capitalize ps-3 mb-0">Maintenance Aset</h6>

                    </div>
                </div>
                <div class="card-body table-responsive">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Tombol Tambah -->
                        <button type="button" class="btn bg-gradient-primary rounded-partner d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                            <i class="fa-solid fa-plus me-1"></i> Tambah
                        </button>

                        <div class="input-group mb-3" style="width: auto;">
                            <span class="input-group-text bg-primary text-white">
                                <i class="fa-solid fa-filter"></i>
                            </span>
                            <select id="filterWaktu" class="form-select"
                                style="box-shadow: none; appearance: none; width: 200px;">
                                <option value="">Semua Waktu</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">1 Tahun</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="maintenanceTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder text-uppercase">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Kondisi</th>
                                    <th>Tanggal Perawatan</th>
                                    <th>Waktu Perawatan</th>
                                    <th>Harga Perawatan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Harga Perawatan:</th>
                                    <th id="totalHarga"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Maintenance -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" aria-labelledby="addMaintenanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('asset.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bolder text-primary text-gradient" id="addMaintenanceModalLabel">
                        Tambah Data Maintenance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Nama Barang</label>
                        <select name="asset_id" id="asset_id" class="form-select" required>
                            <option value="">Pilih Barang</option>
                            <!-- Loop untuk menampilkan nama barang -->
                            @foreach($assets as $asset)
                            <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kondisi</label>
                        <select name="kondisi" id="kondisi" class="form-select" required>
                            <option value="">Pilih Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Sedang">Rusak Sedang</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tanggal Perawatan</label>
                        <input type="date" class="form-control" name="tanggal_perawatan" id="tanggal_perawatan"
                            required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Harga Perawatan</label>
                        <input type="text" name="harga_perawatan" id="harga_perawatan" class="form-control price"
                            placeholder="Tulis harga perawatan" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Jangka Waktu Perawatan</label>
                        <select name="waktu_perawatan" id="waktu_perawatan" class="form-select" required>
                            <option value="">Pilih Waktu</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn bg-gradient-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
    let table = $('#maintenanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('asset.maintenance') }}',
            data: function (d) {
                d.waktu = $('#filterWaktu').val();
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'kondisi', name: 'kondisi' },
            { data: 'tanggal_perawatan', name: 'tanggal_perawatan' },
            {
                data: 'waktu_perawatan',
                name: 'waktu_perawatan',
                render: function(data) {
                    return data ? data + ' Bulan' : '-';
                }
            },
            {
                data: 'harga_perawatan',
                name: 'harga_perawatan',
                render: function(data) {
                    if (!data) data = 0;
                    return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                },
                className: 'text-end',
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function(settings) {
            let api = this.api();
            let total = 0;
            api.column(4, { page: 'current' }).data().each(function(data) {
                if (typeof data === 'string') {
                    data = data.replace(/[^\d]/g, '');
                }
                total += parseInt(data) || 0;
            });
            $('#totalHarga').html('Rp ' + total.toLocaleString('id-ID'));
        }
    });

    // Trigger reload saat filter berubah
    $('#filterWaktu').on('change', function() {
        table.ajax.reload();
    });

    // Format harga input pakai Inputmask
    Inputmask({
        alias: 'currency',
        prefix: 'Rp ',
        groupSeparator: '.',
        digits: 0,
        digitsOptional: false,
        rightAlign: false,
        removeMaskOnSubmit: true,
        autoUnmask: true
    }).mask('.price');
});
</script>
@endpush