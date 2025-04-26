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
                        <h6 class="text-white text-capitalize ps-3 mb-0">Data Maintenance Aset</h6>
                        <div class="pe-3">
                            <select id="filterWaktu" class="form-select form-select-sm">
                                <option value="">Semua Waktu</option>
                                <option value="3">3 Bulan Terakhir</option>
                                <option value="6">6 Bulan Terakhir</option>
                                <option value="12">1 Tahun Terakhir</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive">
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
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
                { data: 'waktu_perawatan', name: 'waktu_perawatan' },
                { data: 'harga_perawatan', name: 'harga_perawatan', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') }
            ],
            drawCallback: function (settings) {
                var api = this.api();
                var total = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                    return (parseInt(a) || 0) + (parseInt(b) || 0);
                }, 0);
                $('#totalHarga').html('Rp ' + total.toLocaleString('id-ID'));
            }
        });

        // Filter waktu
        $('#filterWaktu').change(function () {
            table.ajax.reload();
        });
    });
</script>
@endpush