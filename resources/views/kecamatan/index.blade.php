@extends('layouts.app')

@section('title', 'Kecamatan')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Kecamatan</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Kecamatan</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">List Kecamatan</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                            data-bs-target="#addKecamatanModal">
                            <i class="fa-solid fa-plus"></i> Tambah
                        </button>
                        <table id="kecamatanTable" class="table text-sm">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Kecamatan</th>
                                    <th class="text-uppercase">Jumlah Sekolah</th>
                                    <th class="text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable akan diisi di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Kecamatan -->
    <div class="modal fade" id="addKecamatanModal" aria-labelledby="addKecamatanModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="card card-plain">
                        <form action="{{ route('kecamatan.store') }}" method="POST">
                            @csrf
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Tambah <strong>Kecamatan</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                <label>Nama Kecamatan</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required placeholder="Nama Kecamatan"
                                    autofocus aria-label="Name">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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

    <!-- Modal Edit Kecamatan -->
    <div class="modal fade" id="editKecamatanModal" aria-labelledby="editKecamatanModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="card card-plain">
                        <form id="editKecamatanForm" action="{{ route('kecamatan.update', 'kecamatan_id') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="kecamatan_id" id="kecamatan_id">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient fw-bold mb-2">Edit <strong>Kecamatan</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                <div class="form-group">
                                    <label for="edit_name" class="form-label text-sm fw-semibold">Nama Kecamatan</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                        id="edit_name" value="{{ old('name') }}" required placeholder="Nama Kecamatan"
                                        aria-label="Nama Kecamatan" autocomplete="off">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
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
    $(document).ready(function() {
        $('#kecamatanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('kecamatan.index') }}",
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    className: "text-start"
                },
                {
                    data: 'sekolahs_count',
                    name: 'sekolahs_count',
                    className: "text-start"
                },
                {
                    data: 'action',
                    name: 'action',
                    className: "text-start",
                    orderable: false,
                    searchable: false
                }
            ],
            drawCallback: function() {
                // Inisialisasi ulang tooltip setiap redraw DataTables
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    });

    function deleteKecamatan(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kecamatan akan dihapus permanen dan tag dikembalikan ke Dispora! Pastikan tidak ada sekolah dan aset di kecamatan ini!",
            icon: 'warning',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus',
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function editKecamatan(id, name) {
        $('#editKecamatanModal').modal('show');
        $('#kecamatan_id').val(id);
        $('#edit_name').val(name);
        var actionUrl = "{{ route('kecamatan.update', ':id') }}";
        actionUrl = actionUrl.replace(':id', id);
        $('#editKecamatanForm').attr('action', actionUrl);
    }
</script>
@endpush
