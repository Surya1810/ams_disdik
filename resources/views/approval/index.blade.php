@extends('layouts.app')

@section('title', 'Approval')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Approval</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Approval</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Approval</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <div class="mb-3 d-flex gap-2">
                            <button id="bulk-approve" class="btn btn-success btn-sm">Approve Terpilih</button>
                            <button id="bulk-reject" class="btn btn-danger btn-sm">Reject Terpilih</button>
                            <select id="filter-status" class="form-control w-auto">
                                <option value="">-- Semua Status --</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>

                            <select id="filter-type" class="form-control w-auto">
                                <option value="">-- Semua Jenis --</option>
                                <option value="mutation">Mutation</option>
                                <option value="disposal">Disposal</option>
                                <option value="loan">Loan</option>
                            </select>
                        </div>

                        <!-- Tabel Approval -->
                        <table id="approvalTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Nama Barang</th>
                                    <th>Pengaju</th>
                                    <th>Jenis</th>
                                    <th>Alasan</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
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
    <script>
        $(function() {
            let table = $('#approvalTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('approval.index') }}",
                    data: function(d) {
                        d.status = $('#filter-status').val();
                        d.type = $('#filter-type').val();
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<input type="checkbox" class="select-item" value="${data}">`;
                        }
                    },
                    {
                        data: 'asset',
                        name: 'asset.name'
                    },
                    {
                        data: 'user',
                        name: 'user.name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'keterangan',
                        name: 'payload.keterangan'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Trigger reload saat filter diubah
            $('#filter-status, #filter-type').on('change', function() {
                table.ajax.reload();
            });


            // Select all
            $('#select-all').on('click', function() {
                $('.select-item').prop('checked', this.checked);
            });

            // Approve single
            $('#approvalTable').on('click', '.approve', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Approve?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                }).then(result => {
                    if (result.isConfirmed) {
                        approveRequest([id]);
                    }
                });
            });

            // Reject single
            $('#approvalTable').on('click', '.reject', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Tolak Permintaan?',
                    input: 'text',
                    inputLabel: 'Alasan penolakan',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak'
                }).then(result => {
                    if (result.isConfirmed) {
                        rejectRequest([id], result.value);
                    }
                });
            });

            // Bulk Approve
            $('#bulk-approve').on('click', function() {
                let ids = getSelectedIds();
                if (!ids.length) return Swal.fire('Pilih setidaknya 1 item');
                Swal.fire({
                    title: 'Approve semua terpilih?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya'
                }).then(result => {
                    if (result.isConfirmed) approveRequest(ids);
                });
            });

            // Bulk Reject
            $('#bulk-reject').on('click', function() {
                let ids = getSelectedIds();
                if (!ids.length) return Swal.fire('Pilih setidaknya 1 item');
                Swal.fire({
                    title: 'Tolak semua terpilih?',
                    input: 'text',
                    inputLabel: 'Alasan penolakan',
                    showCancelButton: true,
                    confirmButtonText: 'Tolak'
                }).then(result => {
                    if (result.isConfirmed) rejectRequest(ids, result.value);
                });
            });

            function getSelectedIds() {
                return $('.select-item:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function approveRequest(ids) {
                $.ajax({
                    url: '{{ route('approval.bulk.approve') }}',
                    method: 'POST',
                    data: {
                        ids,
                        _token: '{{ csrf_token() }}'
                    },
                    success: res => {
                        table.ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    }
                });
            }

            function rejectRequest(ids, note) {
                $.ajax({
                    url: '{{ route('approval.bulk.reject') }}',
                    method: 'POST',
                    data: {
                        ids,
                        rejection_note: note,
                        _token: '{{ csrf_token() }}'
                    },
                    success: res => {
                        table.ajax.reload();
                        Swal.fire('Ditolak', res.message, 'success');
                    }
                });
            }
        });
    </script>
@endpush
