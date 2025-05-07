@extends('layouts.app')

@section('title', 'Loan')

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Loan</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Loan</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Loan</h6>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                        data-bs-target="#addLoan"> <i class="fa-solid fa-plus"></i> Buat Pengajuan
                    </button>
                    <!-- Tabel Loan -->
                    <table id="loanTable" class="table text-sm mt-3">
                        <thead class="font-weight-bolder">
                            <tr>
                                <th class="hidden">ID</th>
                                <th class="text-uppercase">Nama Barang</th>
                                <th class="text-uppercase">Diajukan Oleh</th>
                                <th class="text-uppercase">Dari</th>
                                <th class="text-uppercase">Ke</th>
                                <th class="text-uppercase">Waktu</th>
                                <th class="text-uppercase">Status</th>
                                <th class="text-uppercase">Aksi</th>
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

    <!-- Modal Add Loan -->
    <div class="modal fade" id="addLoan" tabindex="-1" aria-labelledby="addLoanLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form id="formPinjam" action="{{ route('approval.store') }}" method="POST" autocomplete="off">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Pinjam <strong>Aset</strong></h4>
                            </div>
                            <div class="card-body">
                                @csrf
                                <input type="hidden" name="type" value="loan">
                                <!-- Pilih Aset -->
                                <div class="mb-3">
                                    <label for="loan_asset_id" class="form-label">Pilih Aset</label>
                                    <select class="form-control asset_id" id="loan_asset_id" name="asset_id" required>
                                        <option value="">Pilih Aset</option>
                                        @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id')==$asset->id ? 'selected' :
                                            '' }}
                                            data-sekolah="{{ $asset->sekolah->name }}"
                                            data-sekolah_id="{{ $asset->sekolah_id }}"
                                            data-kecamatan="{{ $asset->sekolah->kecamatan->name }}"
                                            data-gedung="{{ $asset->gedung }}" data-lantai="{{ $asset->lantai }}"
                                            data-ruangan="{{ $asset->ruangan }}" data-detail="{{ $asset->detail }}">
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

                                <!-- Lokasi Lama -->
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Saat Ini</label>
                                    <div class="row">
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control" id="old_kecamatan"
                                                placeholder="Kecamatan" readonly>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control" id="old_sekolah"
                                                placeholder="Tempat" readonly>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control w-100" id="old_gedung"
                                                placeholder="Gedung" readonly>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control w-100" id="old_lantai"
                                                placeholder="Lantai" readonly>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control w-100" id="old_ruangan"
                                                placeholder="Ruangan" readonly>
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text" class="form-control w-100" id="old_detail"
                                                placeholder="Detail" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lokasi Tujuan -->
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-12 mb-1">
                                            <label for="sekolah_id" class="form-label">Lokasi Tujuan</label>
                                            <select class="form-control sekolah_id" id="sekolah_id" name="sekolah_id"
                                                required>
                                                <option value="">Pilih Sekolah</option>
                                                @foreach ($schools as $school)
                                                <option value="{{ $school->id }}" {{ old('sekolah_id')==$asset->id ?
                                                    'selected' : '' }}>
                                                    {{ $school->kecamatan->name }} - {{ $school->name }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('sekolah_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control w-100 @error('gedung') is-invalid @enderror"
                                                name="gedung" value="{{ old('gedung') }}" placeholder="Gedung Tujuan"
                                                aria-label="gedung" required>
                                            @error('gedung')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control w-100 @error('lantai') is-invalid @enderror"
                                                name="lantai" value="{{ old('lantai') }}" placeholder="Lantai Tujuan"
                                                aria-label="lantai" required>
                                            @error('lantai')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control w-100 @error('ruangan') is-invalid @enderror"
                                                name="ruangan" value="{{ old('ruangan') }}" placeholder="Ruangan Tujuan"
                                                aria-label="ruangan" required>
                                            @error('ruangan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-1">
                                            <input type="text"
                                                class="form-control w-100 @error('detail') is-invalid @enderror"
                                                name="detail" value="{{ old('detail') }}" placeholder="Detail"
                                                aria-label="detail" required>
                                            @error('detail')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Alasan -->
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan / Alasan Peminjaman</label>
                                        <textarea name="keterangan" class="form-control" rows="3"
                                            placeholder="Tulis alasan atau keterangan peminjaman..."></textarea>
                                    </div>
                                </div>

                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <button type="submit" class="btn btn-primary rounded-partner m-0">Ajukan
                                        Pinjam</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script type="text/javascript">
        $('#loan_asset_id').on('change', function () {
            const selected = $(this).find(':selected');
            $('#old_kecamatan').val(selected.data('kecamatan') || '');
            $('#old_sekolah').val(selected.data('sekolah') || '');
            $('#old_gedung').val(selected.data('gedung') || '');
            $('#old_lantai').val(selected.data('lantai') || '');
            $('#old_ruangan').val(selected.data('ruangan') || '');
            $('#old_detail').val(selected.data('detail') || '');
        });
    
        $('.asset_id').select2({
            placeholder: "Pilih Aset",
            dropdownParent: $("#addLoan .modal-content"),
            width: "100%"
        });
    
        $('.sekolah_id').select2({
            placeholder: "Pilih Sekolah",
            dropdownParent: $("#addLoan .modal-content"),
            width: "100%"
        });
    
        $(function () {
            $('#loanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('asset.loan') }}',
                    type: 'GET',
                },
                columns: [
                    { data: 'id', name: 'id', visible: false },
                    { data: 'asset_name', name: 'asset_name' },
                    { data: 'requested_by', name: 'requested_by' },
                    {
                        data: 'from',
                        name: 'from',
                        render: function (data) {
                            if (data && data !== '-') {
                                const parts = data.split('#');
                                return `<div>
                                    <strong>Sekolah:</strong> ${parts[0]}<br>
                                    <strong>Gedung:</strong> ${parts[1]}<br>
                                    <strong>Lantai:</strong> ${parts[2]}<br>
                                    <strong>Ruangan:</strong> ${parts[3]}<br>
                                    <strong>Detail:</strong> ${parts[4]}
                                </div>`;
                            }
                            return '-';
                        },
                        orderable: false,
                    },
                    {
                        data: 'to',
                        name: 'to',
                        render: function (data) {
                            if (data && data !== '-') {
                                const parts = data.split('#');
                                return `<div>
                                    <strong>Sekolah:</strong> ${parts[0]}<br>
                                    <strong>Gedung:</strong> ${parts[1]}<br>
                                    <strong>Lantai:</strong> ${parts[2]}<br>
                                    <strong>Ruangan:</strong> ${parts[3]}<br>
                                    <strong>Detail:</strong> ${parts[4]}
                                </div>`;
                            }
                            return '-';
                        },
                        orderable: false,
                    },
                    { data: 'requested_at', name: 'requested_at' },
                    { data: 'status', name: 'status'},
                    {
                        data: 'id',
                        name: 'action',
                        render: function (data) {
                            return `<a href="loan/pdf/${data}" title="Download PDF" style="color: #dc3545; text-decoration: none;">
                                <i class="fa-solid fa-file-pdf fa-lg"></i>
                            </a>`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [
                    { targets: [0], visible: false }
                ]
            });
        });
    </script>
    @endpush