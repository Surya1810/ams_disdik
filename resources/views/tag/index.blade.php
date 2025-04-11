@extends('layouts.app')

@section('title', 'Tag RFID')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Tag RFID</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Tag RFID</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">List Tag RFID</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        @if (Auth::user()->role_id == 1)
                            <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                                data-bs-target="#addTagModal">
                                <i class="fa-solid fa-plus"></i> Inject
                            </button>
                        @elseif (Auth::user()->role_id == 2)
                            <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                                data-bs-target="#distributeTagModal">
                                <i class="fa-solid fa-share-nodes"></i> Distribusi
                            </button>
                        @endif

                        <table id="tagTable" class="table text-sm mt-3">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">RFID Number</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-uppercase">Kecamatan</th>
                                    <th class="text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Inject (role 1) --}}
    <div class="modal fade" id="addTagModal" aria-labelledby="addTagModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="card card-plain">
                        <form id="addTagForm" method="POST">
                            @csrf
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Tambah <strong>Tag RFID</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Dari Nomor RFID</label>
                                        <input type="number" name="from" class="form-control" required
                                            placeholder="Nomor awal RFID">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Sampai Nomor RFID</label>
                                        <input type="number" name="until" class="form-control" required
                                            placeholder="Nomor akhir RFID">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label>Kecamatan</label>
                                    <input type="text" class="form-control" value="Dispora" disabled>
                                    <input type="hidden" name="kecamatan_id" value="2">
                                </div>
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

    {{-- Modal Distribusi (role 2) --}}
    <div class="modal fade" id="distributeTagModal" aria-labelledby="distributeTagModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="card card-plain">
                        <form id="distributeTagForm" method="POST">
                            @csrf
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Distribusi <strong>Tag RFID</strong></h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="kecamatan_id" class="form-label">Pilih Kecamatan</label>
                                        <select name="kecamatan_id" id="kecamatan_id"
                                            class="form-select kecamatan_id @error('kecamatan_id') is-invalid @enderror"
                                            required>
                                            <option></option>
                                            @foreach ($kecamatan as $k)
                                            @if ($k->id != 1)
                                            <option value="{{ $k->id }}">{{ $k->name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                        @error('kecamatan_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label>Dari Nomor RFID</label>
                                        <input type="number" name="from" class="form-control" required placeholder="Nomor awal RFID">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Sampai Nomor RFID</label>
                                        <input type="number" name="until" class="form-control" required placeholder="Nomor akhir RFID">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1 mb-3">
                                <button type="submit" class="btn btn-primary rounded-partner">Distribusikan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 5px 10px;
        }
    
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            const table = $('#tagTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('tag.index') }}",
                columns: [{
                        data: 'rfid_number',
                        name: 'rfid_number',
                        className: "text-start"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-start"
                    },
                    {
                        data: 'kecamatan',
                        name: 'kecamatan',
                        className: "text-start"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "text-start"
                    }
                ],
                drawCallback: function() {
                    const tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });

            $('.kecamatan_id').select2({
            placeholder: 'Pilih Kecamatan',
            width: '100%'
            });
            
            $('.select-rfid').select2({
            placeholder: 'Pil',
            allowClear: true,
            width: '100%'
            });

            $('.kecamatan_id').select2({
                placeholder: "Pilih Kecamatan",
                dropdownParent: $('#distributeTagModal .modal-content'),
                width: '100%'
            });

            $('#addTagForm').submit(function(event) {
                event.preventDefault();
                $.post("{{ route('tag.store') }}", $(this).serialize())
                    .done(function() {
                        Swal.fire('Sukses!', 'Tag berhasil diinject!', 'success');
                        $('#addTagModal').modal('hide');
                        table.ajax.reload();
                    })
                    .fail(function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan, coba lagi!', 'error');
                    });
            });

            $('#distributeTagForm').submit(function(event) {
                event.preventDefault();
                $.post("{{ route('tag.distribute') }}", $(this).serialize())
                    .done(function() {
                        Swal.fire('Sukses!', 'Tag berhasil didistribusikan!', 'success');
                        $('#distributeTagModal').modal('hide');
                        table.ajax.reload();
                    })
                    .fail(function() {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat distribusi!', 'error');
                    });
            });

            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function deleteTag(rfid_number) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tag ini akan dihapus permanen!",
                icon: 'warning',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Hapus',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/tag/" + rfid_number,
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function() {
                            Swal.fire('Sukses!', 'Tag berhasil dihapus!', 'success');
                            $('#tagTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Tidak dapat menghapus tag!', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
