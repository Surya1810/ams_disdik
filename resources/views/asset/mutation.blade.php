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
                            data-bs-target="#addMutation"> <i class="fa-solid fa-plus"></i> Buat Pengajuan
                        </button>
                        <!-- Tabel Mutation -->
                        <table id="mutationTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Nama Barang</th>
                                    <th class="text-uppercase">Diubah Oleh</th>
                                    <th class="text-uppercase">Dari</th>
                                    <th class="text-uppercase">ke</th>
                                    <th class="text-uppercase">Waktu</th>
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
@endsection

@push('scripts')
    <script type='text/javascript'>
        $(function() {
            $('#mutationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('histories.mutation') }}",
                columns: [{
                        data: 'asset',
                        name: 'asset.name'
                    },
                    {
                        data: 'user',
                        name: 'user.name'
                    },
                    {
                        data: 'dari',
                        name: 'dari',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ke',
                        name: 'ke',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ]
            });
        });
    </script>
@endpush
