@extends('layouts.app')

@section('title')
    Kecamatan
@endsection

@push('css')
@endpush

@section('content')
    <!-- header -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 border-radius-xl position-sticky mt-4 top-1 z-index-sticky shadow-none"
        id="navbarBlur" data-scroll="true" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Halaman</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Kecamatan</li>
                </ol>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex align-items-center">

                </div>
                <ul class="navbar-nav d-flex align-items-center  justify-content-end">
                    <li class="mt-1">
                        <span></span>
                    </li>
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link p-0 text-body" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item px-3 d-flex align-items-center">
                        <a href="{{ route('profile.edit') }}" class="nav-link p-0 text-body">
                            <i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
                        </a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <a href="{{ route('profile.edit') }}" class="nav-link font-weight-bold px-0 text-body">
                            <i class="material-symbols-rounded">account_circle</i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- content -->
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-info shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">List Kecamatan</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <table id="kecamatanTable" class="table text-sm">
                            <thead class="text-uppercase font-weight-bolder">
                                <tr>
                                    <th>Kecamatan</th>
                                    <th>Jumlah Sekolah</th>
                                    <th>Aksi</th>
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

    <!-- footer -->
    <footer class="footer py-4 px-3">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        ©
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                        <a href="https://partnership.co.id">Partnership</a></strong>
                        All rights
                        reserved.
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="nav nav-footer justify-content-center justify-content-lg-end">
                        <p class="text-sm"><i>Your Solution Partner</i></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
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
