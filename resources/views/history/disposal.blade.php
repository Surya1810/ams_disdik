@extends('layouts.app')

@section('title', 'Disposal History')

@section('navbar')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm">
            <a class="text-white opacity-5" href="javascript:;">Halaman</a>
        </li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Disposal History</li>
    </ol>
    <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Disposal History</h6>
</nav>
@endsection

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Disposal History</h6>
                    </div>
                </div>
                <div class="card-body table-responsive pb-2">
                    <table id="disposalHistoryTable" class="table text-sm mt-3">
                        <thead class="font-weight-bolder">
                            <tr>
                                <th class="text-uppercase">RFID Number</th>
                                <th class="text-uppercase">Kode</th>
                                <th class="text-uppercase">Keterangan</th>
                                <th class="text-uppercase">Diajukan Oleh</th>
                                <th class="text-uppercase">Disetujui/Ditolak Oleh</th>
                                <th class="text-uppercase">Jenis</th>
                                <th class="text-uppercase">Status</th>
                                <th class="text-uppercase">Waktu</th>
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
    $(document).ready(function() {
        $('#disposalHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('histories.disposal') }}',
            columns: [
                {
                    data: 'rfid_number',  // Menampilkan keterangan
                    name: 'rfid_number',
                },
                {
                    data: 'kode',  // Menampilkan keterangan
                    name: 'kode',
                },
                {
                    data: 'keterangan',  // Menampilkan keterangan
                    name: 'keterangan',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'requested_by', // Menampilkan nama user
                    name: 'requested_by'
                },
                {
                    data: 'approved_by', // Menampilkan nama user
                    name: 'approved_by'
                },
                {
                    data: 'jenis', // Menampilkan jenis
                    name: 'jenis'
                },
                {
                    data: 'approval', // Menampilkan jenis
                    name: 'approval.status'
                },
                {
                    data: 'created_at', // Menampilkan waktu
                    name: 'histories.created_at'
                },
            ],
            rawColumns: ['status', 'from', 'to']
        });
    });
</script>
@endpush
