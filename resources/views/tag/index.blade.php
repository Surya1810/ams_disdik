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
                        <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                            data-bs-target="#addTagModal">
                            <i class="fa-solid fa-plus"></i> Inject
                        </button>
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

    <!-- Modal Tambah Tag -->
    <div class="modal fade" id="addTagModal" aria-labelledby="addTagModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body py-0">
                    <div class="card card-plain">
                        <form id="addTagForm" action="{{ route('tag.store') }}" method="POST">
                            @csrf
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Tambah <strong>Tag RFID</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                <label>Dari Nomor RFID</label>
                                <input type="number" name="from"
                                    class="form-control @error('from') is-invalid @enderror" value="{{ old('from') }}"
                                    required placeholder="Masukkan nomor awal RFID" autofocus aria-label="From">
                                @error('from')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <label>Sampai Nomor RFID</label>
                                <input type="number" name="until"
                                    class="form-control @error('until') is-invalid @enderror" value="{{ old('until') }}"
                                    required placeholder="Masukkan nomor akhir RFID" aria-label="Until">
                                @error('until')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <label>Pilih Kecamatan</label>
                                <select name="kecamatan_id" class="form-control @error('kecamatan_id') is-invalid @enderror"
                                    required aria-label="Kecamatan ID">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($kecamatan as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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

@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            // Load DataTables
            let table = $('#tagTable').DataTable({
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
                ]
            });

            // Tambah RFID via AJAX
            $('#addTagForm').submit(function(event) {
                event.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('tag.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(response) {
                        Swal.fire('Sukses!', 'Tag berhasil diinject!', 'success');
                        $('#addTagModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan, coba lagi!', 'error');
                    }
                });
            });
        });

        // Fungsi Hapus Tag RFID
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
