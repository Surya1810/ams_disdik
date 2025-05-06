@extends('layouts.app')

@section('title')
Pengguna
@endsection

@push('css')
<style>
    .select2-container .select2-dropdown {
        z-index: 999999 !important;
    }

    .select2-results__options {
        max-height: 250px;
        overflow-y: auto;
    }
</style>
@endpush

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Pengguna</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Pengguna</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">List Pengguna</h6>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                        data-bs-target="#addUserModal"> <i class="fa-solid fa-plus"></i> Tambah
                    </button>
                    <table id="penggunaTable" class="table text-sm">
                        <thead class="font-weight-bolder">
                            <tr>
                                <th class="text-uppercase">Nama</th>
                                <th class="text-uppercase">Kecamatan</th>
                                <th class="text-uppercase">Jumlah Sekolah</th>
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

<!-- Modal Tambah User -->
<div class="modal fade" id="addUserModal" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body py-0">
                <div class="card card-plain">
                    <form id="addUserForm" action="{{ route('user.store') }}" method="POST">
                        @csrf

                        <div class="card-header pb-0 text-start">
                            <h4 class="text-primary text-gradient mb-0">Tambah <strong>User</strong></h4>
                        </div>

                        <div class="card-body mb-3">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Masukkan nama user">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-select select2-role" required>
                                    <option></option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kecamatan_id" class="form-label">Kecamatan</label>
                                <select name="kecamatan_id" class="form-select select2-kecamatan" required>
                                    <option></option>
                                    @foreach ($kecamatans as $kecamatan)
                                    @if ($kecamatan->id != 1)
                                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="Masukkan password user">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" required
                                    placeholder="Konfirmasi password user">
                            </div>
                        </div>

                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <button type="submit" class="btn btn-primary rounded-partner m-0">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body py-0">
                <div class="card card-plain">
                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-header pb-0 text-start">
                            <h4 class="text-primary text-gradient mb-0">Edit <strong>User</strong></h4>
                        </div>

                        <div class="card-body mb-3">
                            <input type="hidden" name="user_id" id="edit_user_id">

                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama</label>
                                <input type="text" id="edit_name" name="name" class="form-control" required
                                    placeholder="Masukkan nama user">
                            </div>

                            <div class="mb-3">
                                <label for="edit_role" class="form-label">Role</label>
                                <select id="edit_role" name="role" class="form-select select2-role" required>
                                    <option></option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="edit_kecamatan_id" class="form-label">Kecamatan</label>
                                <select id="edit_kecamatan_id" name="kecamatan_id" class="form-select select2-kecamatan"
                                    required>
                                    <option></option>
                                    @foreach ($kecamatans as $kecamatan)
                                    @if ($kecamatan->id != 1)
                                    <option value="{{ $kecamatan->id }}">{{ $kecamatan->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required
                                    placeholder="Masukkan password user">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                <input type="password" name="confirm_password" class="form-control" required
                                    placeholder="Konfirmasi password user">
                            </div>
                        </div>

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
<script type="text/javascript">
    $(document).ready(function () {
        const table = $('#penggunaTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.index') }}",
            columns: [
                { data: 'name', name: 'name', className: "text-start" },
                { data: 'kecamatan', name: 'kecamatan', className: "text-start" },
                { data: 'sekolahs_count', name: 'sekolahs_count', className: "text-start" },
                { data: 'assets_count', name: 'assets_count', className: "text-start" },
                { data: 'action', name: 'action', className: "text-start", orderable: false, searchable: false }
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        $('.select2-role').select2({
            placeholder: "Pilih Role",
            dropdownParent: $('#addUserModal'),
            width: '100%'
        });

        $('.select2-kecamatan').select2({
            placeholder: "Pilih Kecamatan",
            dropdownParent: $('#addUserModal'),
            width: '100%'
        });

        $('#editUserModal .select2-role').select2({
            placeholder: "Pilih Role",
            dropdownParent: $('#editUserModal'),
            width: '100%'
        });

        $('#editUserModal .select2-kecamatan').select2({
            placeholder: "Pilih Kecamatan",
            dropdownParent: $('#editUserModal'),
            width: '100%'
        });
    });

    function editPengguna(id) {
        $.get('/user/' + id + '/edit', function (data) {
            $('#editUserForm').attr('action', '/user/' + id);
            $('#editUserForm input[name="name"]').val(data.name);
            $('#editUserForm select[name="role"]').val(data.role_id).trigger('change');
            $('#editUserForm select[name="kecamatan_id"]').val(data.kecamatan_id).trigger('change');
            $('#editUserModal').modal('show');
        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Gagal mengambil data pengguna!'
            });
        });
    }

    function deletePengguna(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data pengguna akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#a9a9a9',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush
