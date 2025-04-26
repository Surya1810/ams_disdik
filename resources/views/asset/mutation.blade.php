@extends('layouts.app')

@section('title', 'Mutation')

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Mutation</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Mutation</h6>
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
                        <h6 class="text-white text-capitalize ps-3 mb-0">Mutasi Aset</h6>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                        data-bs-target="#addPengajuan">
                        <i class="fa-solid fa-plus"></i> Buat Pengajuan
                    </button>

                    <table id="mutationTable" class="table text-sm mt-3">
                        <thead class="font-weight-bolder text-uppercase">
                            <tr>
                                <th>Nama Barang</th>
                                <th>Diajukan Oleh</th>
                                <th>Dari</th>
                                <th>Ke</th>
                                <th>Waktu</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Mutasi --}}
<div class="modal fade" id="addPengajuan" tabindex="-1" aria-labelledby="addPengajuanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form_mutasi" action="{{ route('approval.store') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="type" value="mutation">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pengajuan Mutasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    {{-- Pilih Aset --}}
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Pilih Aset</label>
                        <select class="form-control asset_id" name="asset_id" id="asset_id" required>
                            <option value="">Pilih Aset</option>
                            @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" data-nip="{{ $asset->nip_pic }}"
                                data-nama="{{ $asset->nama_pic }}" data-jabatan="{{ $asset->jabatan_pic }}"
                                data-telp="{{ $asset->telp_pic }}">
                                {{ $asset->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PIC Lama --}}
                    <div class="mb-3">
                        <label class="form-label">PIC Saat Ini</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" id="old_nip" name="old_nip" class="form-control" placeholder="NIP"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="old_nama" name="old_nama" class="form-control" placeholder="Nama"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="old_jabatan" name="old_jabatan" class="form-control"
                                    placeholder="Jabatan" readonly>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="old_telp" name="old_telp" class="form-control"
                                    placeholder="Telepon" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- PIC Baru --}}
                    <div class="mb-3">
                        <label class="form-label">PIC Tujuan</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" name="new_nip" class="form-control" placeholder="NIP" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="new_nama" class="form-control" placeholder="Nama" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="new_jabatan" class="form-control" placeholder="Jabatan"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="new_telp" class="form-control" placeholder="Telepon" min="0"
                                    required>
                            </div>
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div class="mb-3">
                        <label for="detail" class="form-label">Alasan Mutasi</label>
                        <textarea name="detail" class="form-control" rows="3" placeholder="Tulis alasan pengajuan..."
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Ajukan Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Populate old PIC saat pilih asset
        $('#asset_id').on('change', function () {
            const selected = $(this).find(':selected');
            $('#old_nip').val(selected.data('nip') || '');
            $('#old_nama').val(selected.data('nama') || '');
            $('#old_jabatan').val(selected.data('jabatan') || '');
            $('#old_telp').val(selected.data('telp') || '');
        });

        // Select2 init
        $('.asset_id').select2({
            placeholder: "Pilih Aset",
            dropdownParent: $('#addPengajuan'),
            width: "100%"
        });

        // DataTables init
        $('#mutationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('asset.mutation') }}',
                type: 'GET',
            },
            columns: [
                { data: 'asset_name', name: 'asset_name' },
                { data: 'requested_by', name: 'requested_by' },
                {
                    data: function(row) {
                        if (row.from) {
                            return `<div>
                                <strong>NIP:</strong> ${row.from.nip_pic}<br>
                                <strong>Nama:</strong> ${row.from.nama_pic}<br>
                                <strong>Jabatan:</strong> ${row.from.jabatan_pic}<br>
                                <strong>Telp:</strong> ${row.from.telp_pic}
                            </div>`;
                        }
                        return '-';
                    },
                    name: 'from.nip_pic',
                    orderable: false,
                    searchable: false
                },
                {
                    data: function(row) {
                        if (row.to) {
                            return `<div>
                                <strong>NIP:</strong> ${row.to.nip_pic}<br>
                                <strong>Nama:</strong> ${row.to.nama_pic}<br>
                                <strong>Jabatan:</strong> ${row.to.jabatan_pic}<br>
                                <strong>Telp:</strong> ${row.to.telp_pic}
                            </div>`;
                        }
                        return '-';
                    },
                    name: 'to.nip_pic',
                    orderable: false,
                    searchable: false
                },
                { data: 'requested_at', name: 'requested_at' },
                { data: 'status', name: 'status' },
            ],
            rawColumns: ['status', 'from', 'to'] // 'from' dan 'to' berisi HTML di client-side
        });
    });
</script>
@endpush