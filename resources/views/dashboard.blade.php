@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@push('css')
@endpush

@section('navbar')
    <nav aria-label="breadcrumb" class="breadcrumb-fixed">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Dashboard</li>
        </ol>
        <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Dashboard</h6>
                        </div>
                    </div>
                    <div class="card-body pb-2">
                        <h4>Hi! <strong>{{ Auth::user()->name }}</strong></h4>
                        <p>Selamat datang di <strong>Asset Management Information!</strong></p>
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
