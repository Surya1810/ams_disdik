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
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th>Alasan</th>
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
                        data: 'checkbox',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'asset',
                        name: 'asset.name'
                    },
                    {
                        data: 'requester',
                        name: 'requester.name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'keterangan',
                        name: 'payload.keterangan',
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
                    {
                        data: 'rejection_note',
                        name: 'rejection_note',
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
                let checked = $(this).is(':checked');
                $('.row-checkbox').prop('checked', checked);
            });

            // Bulk Approve
            $('#bulk-approve').on('click', function() {
                let ids = getSelectedIds();
                if (!ids.length) return Swal.fire('Warning', 'Pilih setidaknya 1 item', 'warning');
                Swal.fire({
                    title: 'Approve semua terpilih?',
                    icon: 'warning',
                    confirmButtonColor: '#b6d7a8',
                    confirmButtonText: 'Ya'
                }).then(result => {
                    if (result.isConfirmed) approveRequest(ids);
                });
            });

            // Bulk Reject
            $('#bulk-reject').on('click', function() {
                let ids = getSelectedIds();
                if (!ids.length) return Swal.fire('Warning', 'Pilih setidaknya 1 item', 'warning');
                Swal.fire({
                    title: 'Reject semua terpilih?',
                    text: 'Harap sertakan alasan penolakan',
                    input: 'textarea',
                    inputPlaceholder: 'Tulis alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Rejection reason'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda wajib menyertakan alasan!'
                        }
                    },
                    confirmButtonColor: '#ea9999',
                    confirmButtonText: 'Reject'
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
                const formData = new FormData();
                ids.forEach(id => formData.append('ids[]', id));
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route('approval.approve') }}',
                    method: 'POST',
                    data: {
                        ids,
                        _token: '{{ csrf_token() }}'
                    },
                    success: res => {
                        table.ajax.reload();
                        Swal.fire('Berhasil', res.message, 'success');
                    },
                });
            }


            function rejectRequest(ids, note) {
                $.ajax({
                    url: '{{ route('approval.reject') }}',
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
