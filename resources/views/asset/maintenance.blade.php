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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
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

                        <div class="mb-3">
                            <button id="downloadPdf" class="btn btn-danger">
                                <i class="fa-solid fa-file-pdf"></i> Download PDF
                            </button>
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
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        let table = $('#maintenanceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('asset.maintenance') }}',
                data: function(d) {
                    d.waktu = $('#filterWaktu').val(); // Ambil filter waktu
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

                api.rows({ page: 'current' }).data().each(function(row) {
                    let harga = row.harga_perawatan ?? 0;
                    total += parseInt(harga);
                });

                $('#totalHarga').html('Rp ' + total.toLocaleString('id-ID'));
            }
        });

        // Reload table saat filter berubah
        $('#filterWaktu').on('change', function() {
            table.ajax.reload();
        });

        // Tombol download PDF mengikuti filter waktu
        $('#downloadPdf').on('click', function() {
            let waktu = $('#filterWaktu').val();
            let url = '{{ route('maintenance.pdf') }}';
            if (waktu) {
                url += '?waktu=' + waktu;
            }
            window.open(url);
        });

        // Format harga input (kalau kamu nanti punya input form)
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