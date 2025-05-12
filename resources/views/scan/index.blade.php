@extends('layouts.app')

@section('title', 'History Stock Opname')

@push('css')
    <style>
        #filterStatus:hover {
            cursor: pointer;
        }
    </style>
@endpush

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">History Stock Opname</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">History Stock Opname</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12 mb-3 text-center">
                <button class="btn btn-primary rounded-partner" id="refreshButton"><i class="fa-solid fa-arrows-rotate"></i>
                    Refresh Data</button>
            </div>
        </div>
        <div>
            <div class="col-12 mt-5 pt-3">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="text-white text-capitalize ps-3">History Stock Opname</h6>
                                </div>
                                <div class="col-6 text-end ps-3 text-white pe-4">
                                    <small class="float-right">Total Discan: <strong
                                            id="totalRFID">{{ $scansCount }}</strong>
                                        last checked: <strong>
                                            @isset($lastScan->created_at)
                                                {{ $lastScan->created_at }}
                                            @endisset
                                        </strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <table id="scannedTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Total Discan</th>
                                    <th class="text-uppercase">Tempat</th>
                                    <th class="text-uppercase">Kecamatan</th>
                                    <th class="text-uppercase">Waktu</th>
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
@endsection

@push('scripts')
    <script type='text/javascript'>
        const scannedTable = $('#scannedTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('scanned.assets') }}',
            },
            columns: [{
                    data: 'total',
                    name: 'total',
                    className: 'text-center'
                },
                {
                    data: 'place_name',
                    name: 'place_name',
                    className: 'text-center'
                },
                {
                    data: 'district_name',
                    name: 'district_name',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: "text-center"
                },
                {
                    data: 'actions',
                    name: 'asction',
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                }
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    new bootstrap.Tooltip(this);
                });
            }
        });

        // Tambahkan event listener ke tombol refresh
        $('#refreshButton').on('click', function() {
            location.reload();
        });
    </script>
@endpush
