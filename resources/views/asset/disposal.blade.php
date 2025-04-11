@extends('layouts.app')

@section('title', 'Disposal')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Disposal</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Disposal</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Disposal</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                            data-bs-target="#addDisposal"> <i class="fa-solid fa-plus"></i> Buat Pengajuan
                        </button>
                        <!-- Tabel Disposal -->
                        <table id="disposalTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Nama Barang</th>
                                    <th class="text-uppercase">Diajukan Oleh</th>
                                    <th class="text-uppercase">Jenis</th>
                                    <th class="text-uppercase">Keterangan</th>
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

    <!-- Modal Add Disposal -->
    <div class="modal fade" id="addDisposal" tabindex="-1" aria-labelledby="addDisposalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form id="formDisposal" action="{{ route('approval.store') }}" method="POST" autocomplete="off">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Disposal <strong>Aset</strong></h4>
                            </div>
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="type" value="disposal">
                                <!-- Pilih Aset -->
                                <div class="mb-3">
                                    <label for="asset_id" class="form-label">Pilih Aset</label>
                                    <select class="form-control asset_id" id="asset_id" name="asset_id" required>
                                        <option></option>
                                        @foreach ($assets as $asset)
                                            <option value="{{ $asset->id }}"
                                                {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
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

                                <div class="mb-3">
                                    <label for="jenis" class="form-label">Pilih Jenis</label>
                                    <select class="form-control jenis" id="jenis" name="jenis" required>
                                        <option></option>
                                        <option value="Lelang" {{ old('jenis') == 'Lelang' ? 'selected' : '' }}>
                                            Lelang
                                        </option>
                                        <option value="Hilang" {{ old('jenis') == 'Hilang' ? 'selected' : '' }}>
                                            Hilang
                                        </option>
                                    </select>

                                    @error('jenis')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Detail Alasan -->
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Alasan / Keterangan Disposal</label>
                                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Tulis alasan..."></textarea>
                                </div>

                            </div>

                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <button type="submit" class="btn btn-primary rounded-partner m-0">Ajukan Disposal</button>
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
        $('.asset_id').select2({
            placeholder: "Pilih Aset",
            dropdownParent: $("#addDisposal .modal-content"),
            width: "100%"
        })
        $('.jenis').select2({
            placeholder: "Pilih Jenis",
            dropdownParent: $("#addDisposal .modal-content"),
            width: "100%"
        })

        $(document).ready(function() {
            $('#disposalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('asset.disposal') }}',
                columns: [{
                        data: 'asset_name',
                        name: 'asset.name'
                    },
                    {
                        data: 'requested_by',
                        name: 'requestedBy.name'
                    },
                    {
                        data: 'jenis',
                        name: 'payload',
                    },
                    {
                        data: 'keterangan',
                        name: 'payload',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'approvals.created_at'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
