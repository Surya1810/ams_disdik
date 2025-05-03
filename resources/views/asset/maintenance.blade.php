@extends('layouts.app')

@section('title', 'Maintenance')

@push('css')
    <style>
        #filterWaktu:hover {
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
                            <span class="input-group-text bg-primary text-white border-0">
                                <i class="fa-solid fa-filter"></i>
                            </span>
                            <select id="filterWaktu" class="form-select" style="box-shadow: none; appearance: none; width: 200px;">
                                <option value="">Semua Waktu</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">1 Tahun</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <button id="downloadPdf" class="btn btn-primary">
                                <i class="fa-solid fa-file-pdf"></i> Download PDF
                            </button>
                            <button id="markAsMaintained" class="btn btn-primary ms-2">
                                <i class="fa-solid fa-check-circle"></i> Sudah Maintenance
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="maintenanceTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder text-uppercase">
                                <tr>
                                    <th></th> <!-- Kolom checkbox -->
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
                                    <th colspan="5" class="text-end">Total Harga Perawatan:</th>
                                    <th id="totalHarga"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                   <div class="card mt-4 border-0 shadow" style="background-color: #fff3cd;">
                    <div
                        class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center py-4">
                        <h5 class="mb-2 mb-md-0 text-dark fw-semibold">
                            Total Seluruh Harga Perawatan Aset:
                        </h5>
                        <h4 class="text-danger fw-bold mb-0" id="totalHargaCard">Rp 0</h4>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $(document).ready(function() {
    let table = $('#maintenanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('asset.maintenance') }}',
            data: function(d) {
                d.waktu = $('#filterWaktu').val();
            }
        },
        columns: [
            {
                data: null,
                name: 'checkbox',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                if (!row.tanggal_perawatan || row.tanggal_perawatan === '-') return '';
                let parts = row.tanggal_perawatan.split('-');
                let tanggalPerawatan = new Date(parts[2], parts[1] - 1, parts[0]);
                let today = new Date();
                today.setHours(0,0,0,0);
                tanggalPerawatan.setHours(0,0,0,0);

                if (tanggalPerawatan
                <= today) { return `<input type="checkbox" class="maintenance-checkbox" data-id="${row.id}"
                    data-waktu="${row.waktu_perawatan ?? 0}" />`;
                }
                return '';
                }
            },
            { data: 'name', name: 'name' },
            { data: 'kondisi', name: 'kondisi' },
            { data: 'tanggal_perawatan', name: 'tanggal_perawatan' },
            {
                data: 'waktu_perawatan',
                name: 'waktu_perawatan',
                render: function(data) {
                return data ? `${data} bulan` : '-';
                }
            },
            {
                data: 'harga_perawatan',
                name: 'harga_perawatan',
                render: function(data) {
                    data = data || 0;
                    return 'Rp ' + parseInt(data).toLocaleString('id-ID');
                },
                className: 'text-end',
                orderable: false,
                searchable: false
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Format dari controller: d-m-Y
            if (!data.tanggal_perawatan || data.tanggal_perawatan === '-') return;
            let parts = data.tanggal_perawatan.split('-');
            let tanggalPerawatan = new Date(parts[2], parts[1] - 1, parts[0]);
            let today = new Date();
            today.setHours(0,0,0,0);
            tanggalPerawatan.setHours(0,0,0,0);
            if (tanggalPerawatan <= today) {
                $(row).addClass('table-danger');
            }
        },
        drawCallback: function(settings) {
            let api = this.api();
            let total = 0;
            api.rows({ page: 'current' }).data().each(function(row) {
                let harga = row.harga_perawatan ?? 0;
                total += parseInt(harga);
            });
            $('#totalHarga').html('Rp ' + total.toLocaleString('id-ID'));
            $('#totalHargaCard').html('Rp ' + total.toLocaleString('id-ID'));
        }
    });

    $('#filterWaktu').on('change', function() {
        table.ajax.reload();
    });

    $('#downloadPdf').on('click', function() {
        let waktu = $('#filterWaktu').val();
        let url = '{{ route('maintenance.pdf') }}';
        if (waktu) {
            url += '?waktu=' + waktu;
        }
        window.open(url);
    });

    $('#markAsMaintained').on('click', function () {
        let selectedAssets = [];
        $('.maintenance-checkbox:checked').each(function () {
            selectedAssets.push({
                id: $(this).data('id'),
                waktu: parseInt($(this).data('waktu'), 10)
            });
        });
        if (selectedAssets.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih minimal satu aset yang sudah dimaintenance.'
            });
            return;
        }
        $.ajax({
            url: '{{ route('asset.markMaintained') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                assets: selectedAssets
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
                table.ajax.reload();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan: ' + (xhr.responseJSON?.message ?? 'Unknown error')
                });
            }
        });
    });
});
</script>
@endpush
