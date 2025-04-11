@extends('layouts.app')

@section('title', 'Sekolah')

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Sekolah</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Sekolah</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">List Sekolah</h6>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <!-- Tombol Tambah -->
                    <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                        data-bs-target="#addSekolahModal">
                        <i class="fa-solid fa-plus"></i> Tambah
                    </button>

                    <!-- Tabel Data Sekolah -->
                    <table id="sekolahTable" class="table text-sm mt-3">
                        <thead class="font-weight-bolder">
                            <tr>
                                <th class="text-uppercase">Nama Sekolah</th>
                                <th class="text-uppercase">Kategori</th>
                                <th class="text-uppercase">Kecamatan</th>
                                <th class="text-uppercase">Jumlah Aset</th>
                                <th class="text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Sekolah -->
<div class="modal fade" id="addSekolahModal" aria-labelledby="addSekolahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body py-0">
                <div class="card card-plain">
                    <form id="addSekolahForm" action="{{ route('sekolah.store') }}" method="POST">
                        @csrf

                        <!-- Header Modal -->
                        <div class="card-header pb-0 text-start">
                            <h4 class="text-primary text-gradient mb-0">Tambah <strong>Sekolah</strong></h4>
                        </div>

                        <!-- Body Modal -->
                        <div class="card-body mb-3">

                            <!-- Input Kategori -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select name="category" id="category"
                                    class="form-select category @error('category') is-invalid @enderror" required>
                                    <option></option>
                                    <option value="SD" {{ old('category')=='SD' ? 'selected' : '' }}>SD</option>
                                    <option value="SMP" {{ old('category')=='SMP' ? 'selected' : '' }}>SMP</option>
                                    <option value="SMA" {{ old('category')=='SMA' ? 'selected' : '' }}>SMA</option>
                                    <option value="SMK" {{ old('category')=='SMK' ? 'selected' : '' }}>SMK</option>
                                    <option value="MA" {{ old('category')=='MA' ? 'selected' : '' }}>MA</option>
                                </select>
                                @error('category')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <!-- Input Nama Sekolah -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Sekolah</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Masukkan nama sekolah" required autofocus>
                                @error('name')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <!-- Input Kecamatan -->
                            <div class="mb-3">
                                <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                <select name="kecamatan_id"
                                    class="form-select @error('kecamatan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach ($kecamatans as $kecamatan)
                                    @if ($kecamatan->id != 1)
                                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                <span class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Footer Modal -->
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <button type="submit" class="btn btn-primary rounded-partner m-0">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Sekolah -->
<div class="modal fade" id="editSekolahModal" aria-labelledby="editSekolahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body py-0">
                <div class="card card-plain">
                    <form id="editSekolahForm" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Header Modal -->
                        <div class="card-header pb-0 text-start">
                            <h4 class="text-primary text-gradient mb-0">Edit <strong>Sekolah</strong></h4>
                        </div>

                        <!-- Body Modal -->
                        <div class="card-body mb-3">
                            <input type="hidden" id="edit_sekolah_id" name="sekolah_id">

                            <!-- Input Kategori -->
                            <div class="mb-3">
                                <label for="edit_category" class="form-label">Kategori</label>
                                <select id="edit_category" name="category" class="form-select category" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                    <option value="MA">MA</option>
                                </select>
                            </div>

                            <!-- Input Nama Sekolah -->
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama Sekolah</label>
                                <input type="text" id="edit_name" name="name" class="form-control" required
                                    placeholder="Masukkan nama sekolah">
                            </div>

                            <!-- Input Kecamatan -->
                            <div class="mb-3">
                                <label for="edit_kecamatan_id" class="form-label">Kecamatan</label>
                                <select id="edit_kecamatan_id" name="kecamatan_id" class="form-select" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach ($kecamatans as $kecamatan)
                                    @if ($kecamatan->id != 1)
                                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Footer Modal -->
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <button type="submit" class="btn btn-primary rounded-partner m-0">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        // Inisialisasi DataTable
        $('#sekolahTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sekolah.index') }}",
            columns: [
                { data: 'name', name: 'name', className: "text-start" },
                { data: 'category', name: 'category', className: "text-start" },
                { data: 'kecamatan', name: 'kecamatan', className: "text-start" },
                { data: 'assets_count', name: 'assets_count', className: "text-start" },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-start" }
            ],
            drawCallback: function () {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });

        // Inisialisasi Select2
        $('.category').select2({
            placeholder: "Pilih Kategori",
            dropdownParent: $('#addSekolahModal .modal-content'),
            width: '100%'
        });
        $('#editSekolahModal .category').select2({
            placeholder: "Pilih Kategori",
            dropdownParent: $('#editSekolahModal .modal-content'),
            width: '100%'
        });

        $('select[name="kecamatan_id"]', '#addSekolahModal').select2({
            placeholder: "Pilih Kecamatan",
            dropdownParent: $('#addSekolahModal .modal-content'),
            width: '100%'
        });
        $('select[name="kecamatan_id"]', '#editSekolahModal').select2({
            placeholder: "Pilih Kecamatan",
            dropdownParent: $('#editSekolahModal .modal-content'),
            width: '100%'
        });
    });

    function editSekolah(id, name, kecamatan_id, category) {
        $('#editSekolahModal').modal('show');
        $('#edit_sekolah_id').val(id);
        $('#edit_name').val(name);
        $('#edit_kecamatan_id').val(kecamatan_id).trigger('change');
        $('#edit_category').val(category).trigger('change');

        let actionUrl = "{{ route('sekolah.update', ':id') }}".replace(':id', id);
        $('#editSekolahForm').attr('action', actionUrl);
    }

    function deleteSekolah(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data sekolah ini akan dihapus!",
            icon: 'warning',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush