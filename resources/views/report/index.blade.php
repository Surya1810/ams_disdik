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
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                                <i class="fa-solid fa-boxes-packing"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Total Semua Aset</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['assetsCount'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-lg">
                                                <i class="fa-solid fa-boxes-packing"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Total Aset Hilang</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['kehilanganCount'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-warning shadow text-center border-radius-lg">
                                                <i class="fas fa-boxes-packing opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Total Aset Dirawat</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['perawatanCount'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header mx-4 p-3 d-flex justify-content-center align-items-center">
                                        <div
                                            class="icon icon-shape icon-lg bg-gradient-success shadow text-center border-radius-lg">
                                            <i class="fa-solid fa-tags"></i>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Total Tag RFID</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['tagsCount'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-lg-0 mb-4">
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                                <i class="fas fa-boxes-packing opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Nilai Semua Aset</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['assetsNilai'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-lg">
                                                <i class="fas fa-boxes-packing opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Nilai Aset Hilang</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['kehilanganNilai'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-warning shadow text-center border-radius-lg">
                                                <i class="fas fa-boxes-packing opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Nilai Perawatan Aset</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['perawatanNilai'] }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-header p-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div
                                                class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-lg">
                                                <i class="fas fa-tags opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0 p-3 text-center">
                                        <h6 class="text-center mb-0">Total Tag Digunakan</h6>
                                        <hr class="horizontal dark my-3">
                                        <h6 class="mb-0">{{ $data['tagsUsedCount'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">Nilai Aset Per Tahun</h6>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="d-flex justify-content-between">
                            <div class="col-md-4">
                                <select id="filterTahun" class="form-select select2">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($data['tahunPembelianArr'] as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2 text-end fw-bold small" id="totalNilaiKeseluruhanPerTahun">
                                Nilai: <span class="text-primary">Rp0</span>
                            </div>
                        </div>
                        <div class="p-2 mb-3 overflow-x-scroll">
                            <table id="tableAsetPerTahun" class="table text-sm mt-3">
                                <thead class="font-weight-bolder">
                                    <tr>
                                        <th class="text-uppercase">Tahun</th>
                                        <th class="text-uppercase">Total Aset</th>
                                        <th class="text-uppercase">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data Aset Per Tahun --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if (auth()->user()->role_id == 3)
            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-header pb-0 px-3">
                        <h6 class="mb-0">Nilai Aset Per Sekolah</h6>
                    </div>
                    <div class="card-body pt-4 p-3">
                        <div class="d-flex justify-content-between">
                            <div class="col-md-4">
                                <select id="filterSekolah" class="form-select select2">
                                    <option value="">Semua Sekolah</option>
                                    @foreach ($data['sekolahOrKecamatanArr'] as $item)
                                    <option value="{{ $item->id }}">{{ $item->category . ' ' . $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-2 text-end fw-bold small" id="totalNilaiKeseluruhanPerSekolah">
                                Nilai: <span class="text-primary">Rp0</span>
                            </div>
                        </div>
                        <div class="p-2 mb-3 overflow-x-scroll">
                            <table id="tableAsetPerSekolah" class="table text-sm mt-3">
                                <thead class="font-weight-bolder">
                                    <tr>
                                        <th class="text-uppercase">Tahun</th>
                                        <th class="text-uppercase">Sekolah</th>
                                        <th class="text-uppercase">Total Aset</th>
                                        <th class="text-uppercase">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data Aset Per Tahun --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endsection

    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                // Select 2
                $('.select2').select2();

                let tableAsetPerTahun = $('#tableAsetPerTahun').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5], [5]],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('report.json.aset-per-tahun') }}",
                        data: function (d) {
                            d.tahun = $('#filterTahun').find(':selected').val();
                        },
                        dataSrc: function (json) {
                            $('#totalNilaiKeseluruhanPerTahun span').text(json.total_all_nilai || 'Rp0');
                            return json.data;
                        }
                    },
                    columns: [{
                            data: 'tahun_pembelian',
                            name: 'tahun_pembelian',
                            className: "text-start"
                        },
                        {
                            data: 'total_asset',
                            name: 'total_asset',
                            className: "text-start"
                        },
                        {
                            data: 'total_nilai',
                            name: 'total_nilai',
                            className: "text-start",
                        }
                    ]
                });

                // Trigger change filter tahun
                $('#filterTahun').on('change', function() {
                    tableAsetPerTahun.ajax.reload();
                });

            });
        </script>

        @if (auth()->user()->role_id == 3)
        <script type="text/javascript">
            $(document).ready(function() {
                let tableAsetPerSekolah = $('#tableAsetPerSekolah').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5], [5]],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('report.json.aset-per-sekolah') }}",
                        data: function (d) {
                            d.sekolah = $('#filterSekolah').find(':selected').val();
                        },
                        dataSrc: function (json) {
                            $('#totalNilaiKeseluruhanPerSekolah span').text(json.total_all_nilai || 'Rp0');
                            return json.data;
                        }
                    },
                    columns: [
                        {
                            data: 'tahun_pembelian',
                            name: 'tahun_pembelian',
                            className: "text-start"
                        },
                        {
                            data: 'nama_sekolah',
                            name: 'nama_sekolah',
                            className: "text-start"
                        },
                        {
                            data: 'total_asset',
                            name: 'total_asset',
                            className: "text-start"
                        },
                        {
                            data: 'total_nilai',
                            name: 'total_nilai',
                            className: "text-start",
                        }
                    ]
                });

                // Trigger change filter sekolah
                $('#filterSekolah').on('change', function() {
                    tableAsetPerSekolah.ajax.reload();
                });
            });
        </script>
        @endif
    @endpush
