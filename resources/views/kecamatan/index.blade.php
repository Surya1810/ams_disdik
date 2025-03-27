@extends('layouts.app')

@section('title')
    Kecamatan
@endsection

@push('css')
@endpush

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Kecamatan</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Kecamatan</h6>
    </nav>
@endsection

@section('content')
    <!-- content -->
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
                            data-bs-target="#addKecamatan"> <i class="fa-solid fa-plus"></i> Tambah
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

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Kecamatan -->
    <div class="modal fade" id="addKecamatan" aria-labelledby="addKecamatan" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form action="{{ route('kecamatan.store') }}" method="POST">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Tambah <strong>Kecamatan</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                @csrf
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
                                <button type="submit" class="btn btn-primary  rounded-partner m-0">Simpan</button>
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
                columns: [{
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
                ]
            });
        });

        function deleteKecamatan(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data kecamatan & sekolah akan dihapus permanen!",
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
