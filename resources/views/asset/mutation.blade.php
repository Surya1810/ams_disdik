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
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Mutation</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                            data-bs-target="#addPengajuan"> <i class="fa-solid fa-plus"></i> Buat Pengajuan
                        </button>
                        <!-- Tabel Mutation -->
                        <table id="mutationTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Nama Barang</th>
                                    <th class="text-uppercase">Diajukan Oleh</th>
                                    <th class="text-uppercase">Dari</th>
                                    <th class="text-uppercase">ke</th>
                                    <th class="text-uppercase">Waktu</th>
                                    <th class="text-uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Mutasi -->
    <div class="modal fade" id="addPengajuan" tabindex="-1" aria-labelledby="addPengajuanLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form id="formMutasi" action="{{ route('approval.store') }}" method="POST" autocomplete="off">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Mutasi <strong>Aset</strong></h4>
                            </div>
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="type" value="mutation">
                                <!-- Pilih Aset -->
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Pilih Aset</label>
                                    <select class="form-control asset_id" id="asset_id" name="asset_id" required>
                                        <option></option>
                                        @foreach ($assets as $asset)
                                            <option value="{{ $asset->id }}"
                                                {{ old('asset_id') == $asset->id ? 'selected' : '' }}
                                                data-nip="{{ $asset->nip_pic }}" data-nama="{{ $asset->nama_pic }}"
                                                data-jabatan="{{ $asset->jabatan_pic }}" data-telp="{{ $asset->telp_pic }}">
                                                {{ $asset->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('asset_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- PIC Lama -->
                                <div class="mb-3">
                                    <label class="form-label">PIC Saat Ini</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control mb-1" id="old_nip" placeholder="NIP"
                                                readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control mb-1" id="old_nama"
                                                placeholder="Nama" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control mb-1" id="old_jabatan"
                                                placeholder="Jabatan" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="old_telp" placeholder="Telepon"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- PIC Tujuan -->
                                <div class="mb-3">
                                    <label class="form-label">PIC Tujuan</label>
                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control @error('nip_pic') is-invalid @enderror" name="nip_pic"
                                                value="{{ old('nip_pic') }}" placeholder="NIP PIC tujuan"
                                                aria-label="nip_pic" required>
                                            @error('nip_pic')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control @error('nama_pic') is-invalid @enderror"
                                                name="nama_pic" value="{{ old('nama_pic') }}"
                                                placeholder="Nama PIC tujuan" aria-label="nama_pic" required>
                                            @error('nama_pic')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control @error('jabatan_pic') is-invalid @enderror"
                                                name="jabatan_pic" value="{{ old('jabatan_pic') }}"
                                                placeholder="Jabatan PIC tujuan" aria-label="jabatan_pic" required>
                                            @error('jabatan_pic')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="number"
                                                class="form-control @error('telp_pic') is-invalid @enderror"
                                                name="telp_pic" value="{{ old('telp_pic') }}"
                                                placeholder="Telepon PIC tujuan" aria-label="telp_pic" min=0 required>
                                            @error('telp_pic')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Alasan -->
                                <div class="mb-3">
                                    <label for="detail" class="form-label">Alasan Mutasi</label>
                                    <textarea name="detail" id="detail" class="form-control" rows="3" placeholder="Tulis alasan..."></textarea>
                                </div>

                            </div>

                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <button type="submit" class="btn btn-primary rounded-partner m-0">Ajukan Mutasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type='text/javascript'>
        $('#asset_id').on('change', function() {
            const selected = $(this).find(':selected');
            $('#old_nip').val(selected.data('nip') || '');
            $('#old_nama').val(selected.data('nama') || '');
            $('#old_jabatan').val(selected.data('jabatan') || '');
            $('#old_telp').val(selected.data('telp') || '');
        });
        $('.asset_id').select2({
            placeholder: "Pilih Aset",
            dropdownParent: $("#addPengajuan .modal-content"),
            width: "100%"
        })

        $(function() {
            $('#mutationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('asset.mutation') }}',
                columns: [{
                        data: 'asset_name',
                        name: 'asset.name'
                    },
                    {
                        data: 'requested_by',
                        name: 'requester.name'
                    },
                    {
                        data: 'from',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'to',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'requested_at',
                        name: 'approvals.created_at'
                    },
                    {
                        data: 'status',
                        name: 'approvals.status'
                    },
                ]
            });
        });
    </script>
@endpush
