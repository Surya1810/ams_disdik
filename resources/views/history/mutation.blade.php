@extends('layouts.app')

@section('title', 'Mutation History')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Mutation History</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Mutation History</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">History</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <!-- Tabel Mutation History -->
                        <table id="mutationTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">Nama Sekolah</th>
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
        $(document).ready(function() {
            $('#mutationTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sekolah.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name',
                        className: "text-start"
                    },
                    {
                        data: 'category',
                        name: 'category',
                        className: "text-start"
                    },
                    {
                        data: 'kecamatan',
                        name: 'kecamatan',
                        className: "text-start"
                    },
                    {
                        data: 'assets_count',
                        name: 'assets_count',
                        className: "text-start"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
