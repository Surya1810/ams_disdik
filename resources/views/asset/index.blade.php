@extends('layouts.app')

@section('title')
    Asset
@endsection

@push('css')
    <style>
        #preview {
            text-align: center;
        }

        #previewImg {
            max-height: 200px;
            max-width: 200px;
            width: auto;
            display: block;
            margin: 0 auto;
            /* center image */
        }

        #filterWrapper select:hover {
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
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Asset</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Asset</h6>
    </nav>
@endsection

@section('content')
    <!-- content -->
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">List Asset</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        @if (session('list_errors'))
                            <div class="alert alert-warning alert-dismissible fade show">
                                <button type="button" class="btn-close fw-bold text-dark" data-bs-dismiss="alert"
                                    aria-label="Close">
                                    &times;
                                </button>
                                <strong>Beberapa data gagal diimport:</strong>
                                <ul class="overflow-y-auto" style="max-height: 100px">
                                    @foreach (session('list_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between" id="buttonWrapper">
                            <div id="leftButtonWrapper" class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                <button type="button" class="btn bg-gradient-primary rounded-partner mb-0"
                                    data-bs-toggle="modal" data-bs-target="#addAset"> <i class="fa-solid fa-plus"></i>
                                    Tambah
                                </button>
                                <div class="d-flex gap-2" id="filterWrapper">
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white border-0">
                                            <i class="fa-solid fa-filter"></i>
                                        </span>
                                        {{-- Filter Kondisi --}}
                                        <select id="filterKondisi" class="form-control w-auto ps-2">
                                            <option value="">Semua Kondisi</option>
                                            <option value="Baik">
                                                Baik
                                            </option>
                                            <option value="Perlu Perbaikan">
                                                Perlu Perbaikan
                                            </option>
                                            <option value="Rusak Ringan">
                                                Rusak Ringan
                                            </option>
                                            Rusak Sedang
                                            </option>
                                            <option value="Rusak Berat">
                                                Rusak Berat
                                            </option>
                                        </select>
                                    </div>
                                    {{-- Filter Tempat/Lokasi/Sekolah --}}
                                    <select id="filterTempat" class="form-control w-auto">
                                        <option value="">Semua Tempat </option>
                                        @if (auth()->user()->role_id == 2)
                                            {{-- Untuk role == 2 atau Dispora --}}
                                            @foreach ($placesForFilter as $place)
                                                <option value="{{ $place->id }}"
                                                    {{ old('sekolah_id') == $place->id ? 'selected' : '' }}>
                                                    {{ $place->category . ' ' . $place->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach ($places as $place)
                                                <option value="{{ $place->id }}"
                                                    {{ old('sekolah_id') == $place->id ? 'selected' : '' }}>
                                                    {{ $place->category . ' ' . $place->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    {{-- Filter Tahun --}}
                                    <select id="filterTahun" class="form-control w-auto">
                                        <option value="">Semua Tahun</option>
                                        @foreach ($tahunPembelianArr as $tahun)
                                            <option value="{{ $tahun }}">
                                                {{ $tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="rightButtonWrapper" class="ms-auto">
                                <button type="button" class="btn bg-gradient-success rounded-partner" id="buttonExport"
                                    {{ $countAssets > 0 ? '' : 'disabled' }}>
                                    <i class="fa-solid fa-download"></i> Export
                                </button>
                                <button type="button" class="btn bg-gradient-warning rounded-partner"
                                    id="buttonShowImportModal"> <i class="fa-solid fa-upload"></i>
                                    Import
                                </button>
                            </div>
                        </div>
                        <table id="asetTable" class="table text-sm">
                            <thead class="font-weight-bolder">
                                <tr>
                                    <th class="text-uppercase">RFID</th>
                                    <th class="text-uppercase">Kode Barang</th>
                                    <th class="text-uppercase">Nama/Jenis Barang</th>
                                    <th class="text-uppercase">Merk/Type</th>
                                    <th class="text-uppercase">Tahun Pembelian</th>
                                    <th class="text-uppercase">Kondisi</th>
                                    <th class="text-uppercase">Tempat</th>
                                    @if (auth()->user()->role_id == 2)
                                        <th class="text-uppercase">Kecamatan</th>
                                    @endif
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

    <!-- Modal Add Aset -->
    <div class="modal fade" id="addAset" aria-labelledby="addAset" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form action="{{ route('asset.store') }}" method="POST" autocomplete="off"
                            enctype="multipart/form-data">
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Tambah <strong>Aset</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                @csrf
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h6>Informasi Barang</h6>
                                    </div>
                                    <div class="col-4">
                                        <label for="image">Pilih Foto Awal:</label>
                                        <input type="file" name="image" accept="image/*" class="form-control"
                                            onchange="previewImage(event)" data-edit="0">

                                        <div id="preview" style="margin-top: 15px;">
                                            <img id="previewImg" src="" alt="Preview" style="display: none;" />
                                            <p id="fileName" style="margin-top: 5px;"></p>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <label>Nomor RFID</label>
                                                <select class="form-control select2 tag" id="tag" name="tag" required>
                                                    <option></option>
                                                    @foreach ($tags as $tag)
                                                        <option value="{{ $tag }}"
                                                            {{ old('tag') == $tag ? 'selected' : '' }}>
                                                            {{ $tag }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tag')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label>Kode Barang</label>
                                                <input type="text"
                                                    class="form-control @error('kode') is-invalid @enderror"
                                                    name="kode" value="{{ old('kode') }}" required
                                                    placeholder="Tulis kode barang" aria-label="kode">
                                                @error('kode')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label>Nama/Jenis Barang</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name') }}" required
                                                    placeholder="Tulis nama/jenis barang" aria-label="name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Nomor Register</label>
                                                <input type="text"
                                                    class="form-control @error('register') is-invalid @enderror"
                                                    name="register" value="{{ old('register') }}" required
                                                    placeholder="Tulis nomor register barang" aria-label="register">
                                                @error('register')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Merk/Type</label>
                                                <input type="text"
                                                    class="form-control @error('merk') is-invalid @enderror"
                                                    name="merk" value="{{ old('merk') }}" required
                                                    placeholder="Tulis merk/type barang" aria-label="merk">
                                                @error('merk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Ukuran/cc <small class="text-danger">*optional</small></label>
                                                <input type="text"
                                                    class="form-control @error('ukuran') is-invalid @enderror"
                                                    name="ukuran" value="{{ old('ukuran') }}"
                                                    placeholder="Tulis ukuran/cc barang" aria-label="ukuran">
                                                @error('ukuran')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Bahan</label>
                                                <input type="text"
                                                    class="form-control @error('bahan') is-invalid @enderror"
                                                    name="bahan" value="{{ old('bahan') }}"
                                                    placeholder="Tulis bahan barang" aria-label="bahan" required>
                                                @error('bahan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-6 col-md-4">
                                                <label>Tahun Pembelian</label>
                                                <input type="numeric"
                                                    class="form-control @error('tahun_pembelian') is-invalid @enderror"
                                                    placeholder="{{ date('Y') }}" name="tahun_pembelian"
                                                    value="{{ old('tahun_pembelian') }}" aria-label="tahun_pembelian"
                                                    required>
                                                @error('tahun_pembelian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Pabrik <small class="text-danger">*optional</small></label>
                                                <input type="text"
                                                    class="form-control @error('pabrik') is-invalid @enderror"
                                                    name="pabrik" value="{{ old('pabrik') }}"
                                                    placeholder="Tulis pabrik asal barang" aria-label="pabrik">
                                                @error('pabrik')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Nomor Barang</h6>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Rangka <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('rangka') is-invalid @enderror"
                                            name="rangka" value="{{ old('rangka') }}"
                                            placeholder="Tulis nomor rangka barang" aria-label="rangka">
                                        @error('rangka')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Mesin <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('mesin') is-invalid @enderror"
                                            name="mesin" value="{{ old('mesin') }}"
                                            placeholder="Tulis nomor mesin barang" aria-label="mesin">
                                        @error('mesin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Polisi <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('polisi') is-invalid @enderror"
                                            name="polisi" value="{{ old('polisi') }}"
                                            placeholder="Tulis nomor polisi barang" aria-label="polisi">
                                        @error('polisi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>BPKB <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('bpkb') is-invalid @enderror"
                                            name="bpkb" value="{{ old('bpkb') }}"
                                            placeholder="Tulis nomor BPKB barang" aria-label="bpkb">
                                        @error('bpkb')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>PIC</h6>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>NIP</label>
                                        <input type="text" class="form-control @error('nip_pic') is-invalid @enderror"
                                            name="nip_pic" value="{{ old('nip_pic') }}"
                                            placeholder="Tulis NIP PIC barang" aria-label="nip_pic" required>
                                        @error('nip_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Nama</label>
                                        <input type="text"
                                            class="form-control @error('nama_pic') is-invalid @enderror" name="nama_pic"
                                            value="{{ old('nama_pic') }}" placeholder="Tulis nama PIC barang"
                                            aria-label="nama_pic" required>
                                        @error('nama_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Jabatan</label>
                                        <input type="text"
                                            class="form-control @error('jabatan_pic') is-invalid @enderror"
                                            name="jabatan_pic" value="{{ old('jabatan_pic') }}"
                                            placeholder="Tulis jabatan PIC barang" aria-label="jabatan_pic" required>
                                        @error('jabatan_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Telepon</label>
                                        <input type="number"
                                            class="form-control @error('telp_pic') is-invalid @enderror" name="telp_pic"
                                            value="{{ old('telp_pic') }}" placeholder="Tulis nomor telepon PIC barang"
                                            aria-label="telp_pic" min=0 required>
                                        @error('telp_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Perawatan Barang</h6>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Asal-usul Perolehan</label>
                                        <input type="text"
                                            class="form-control @error('asal_perolehan') is-invalid @enderror"
                                            name="asal_perolehan" value="{{ old('asal_perolehan') }}"
                                            placeholder="Tulis asal-usul perolehan barang" aria-label="asal_perolehan"
                                            required>
                                        @error('asal_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Nilai Perolehan</label>
                                        <input type="text" name="nilai_perolehan"
                                            class="form-control price @error('nilai_perolehan') is-invalid @enderror"
                                            placeholder="Tulis harga perawatan barang"
                                            value="{{ old('nilai_perolehan') }}" min="0" step="0.01"
                                            aria-label="nilai_perolehan" required>
                                        @error('nilai_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Kondisi</label>
                                        <select name="kondisi" id="kondisi"
                                            class="form-select kondisi @error('kondisi') is-invalid @enderror" required>
                                            <option></option>
                                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>
                                                Baik
                                            </option>
                                            <option value="Perlu Perbaikan"
                                                {{ old('kondisi') == 'Perlu Perbaikan' ? 'selected' : '' }}>
                                                Perlu Perbaikan
                                            </option>
                                            <option value="Rusak Ringan"
                                                {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>
                                                Rusak Ringan
                                            </option>
                                            <option value="Rusak Sedang"
                                                {{ old('kondisi') == 'Rusak Sedang' ? 'selected' : '' }}>
                                                Rusak Sedang
                                            </option>
                                            <option value="Rusak Berat"
                                                {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>
                                                Rusak Berat
                                            </option>
                                        </select>

                                        @error('kondisi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Tanggal Perawatan</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_perawatan') is-invalid @enderror"
                                            name="tanggal_perawatan" value="{{ old('tanggal_perawatan') }}"
                                            aria-label="tanggal_perawatan" required>
                                        @error('tanggal_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Harga Perawatan</label>
                                        <input type="text" name="harga_perawatan"
                                            class="form-control price @error('harga_perawatan') is-invalid @enderror"
                                            placeholder="Tulis harga perawatan barang"
                                            value="{{ old('harga_perawatan') }}" min="0" step="0.01"
                                            aria-label="harga_perawatan" required>
                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Jangka Waktu Perawatan</label>
                                        <select name="waktu_perawatan" id="waktu_perawatan"
                                            class="form-control waktu_perawatan @error('waktu_perawatan') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Jangka Waktu</option>
                                            @for ($i = 1; $i <= 48; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('waktu_perawatan') == $i ? 'selected' : '' }}>
                                                    {{ $i }} Minggu
                                                </option>
                                            @endfor
                                        </select>

                                        @error('waktu_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Lokasi</h6>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Kecamatan</label>
                                        <input type="text" class="form-control"
                                            value="{{ Auth::user()->kecamatan->name }}" disabled>

                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label>Tempat</label>
                                        <select name="sekolah_id" id="sekolah_id"
                                            class="form-control place @error('sekolah_id') is-invalid @enderror" required>
                                            <option></option>
                                            @foreach ($places as $place)
                                                <option value="{{ $place->id }}"
                                                    {{ old('sekolah_id') == $place->id ? 'selected' : '' }}>
                                                    {{ $place->category . ' ' . $place->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('sekolah_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Gedung</label>
                                        <input type="text" class="form-control @error('gedung') is-invalid @enderror"
                                            name="gedung" value="{{ old('gedung') }}" placeholder="Tulis lokasi gedung"
                                            aria-label="gedung" required>
                                        @error('gedung')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label>Lantai</label>
                                        <input type="text" class="form-control @error('lantai') is-invalid @enderror"
                                            name="lantai" value="{{ old('lantai') }}" placeholder="Tulis lokasi lantai"
                                            aria-label="lantai" required>
                                        @error('lantai')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label>Ruangan</label>
                                        <input type="text" class="form-control @error('ruangan') is-invalid @enderror"
                                            name="ruangan" value="{{ old('ruangan') }}"
                                            placeholder="Tulis lokasi ruangan" aria-label="ruangan" required>
                                        @error('ruangan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label>Detail</label>
                                        <input type="text" class="form-control @error('detail') is-invalid @enderror"
                                            name="detail" value="{{ old('detail') }}" placeholder="Tulis lokasi detail"
                                            aria-label="detail" required>
                                        @error('detail')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <button type="submit" class="btn btn-primary  rounded-partner m-0">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Aset -->
    <div class="modal fade" id="editAset" aria-labelledby="editAsetLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form id="form-edit-asset" method="POST" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-header pb-0 text-left">
                                <h4 class="text-primary text-gradient">Edit <strong>Aset</strong></h4>
                            </div>
                            <div class="card-body mb-3">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h6>Informasi Barang</h6>
                                    </div>
                                    <div class="col-4">
                                        <label for="image">Pilih Foto Awal:</label>
                                        <input type="file" name="image" accept="image/*" class="form-control"
                                            onchange="previewImage(event)" data-edit="1">

                                        <div id="preview" style="margin-top: 15px;">
                                            <img id="previewImgEdit" src="" alt="Preview"
                                                style="display: none; max-width: 100%; height: auto;" />
                                            <p id="fileNameEdit" style="margin-top: 5px;"></p>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <label>Nomor RFID</label>
                                                <input type="text"
                                                    class="form-control muted @error('tag') is-invalid @enderror"
                                                    name="tag" id="tag_edit" readonly placeholder=""
                                                    aria-label="tag">
                                                @error('tag')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label>Kode Barang</label>
                                                <input type="text"
                                                    class="form-control @error('kode') is-invalid @enderror"
                                                    name="kode" id="kode" required
                                                    placeholder="Tulis kode barang" aria-label="kode">
                                                @error('kode')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label>Nama/Jenis Barang</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" id="name" required
                                                    placeholder="Tulis nama/jenis barang" aria-label="name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Nomor Register</label>
                                                <input type="text"
                                                    class="form-control @error('register') is-invalid @enderror"
                                                    name="register" id="register" required
                                                    placeholder="Tulis nomor register barang" aria-label="register">
                                                @error('register')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Merk/Type</label>
                                                <input type="text"
                                                    class="form-control @error('merk') is-invalid @enderror"
                                                    name="merk" id="merk" required
                                                    placeholder="Tulis merk/type barang" aria-label="merk">
                                                @error('merk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Ukuran/cc <small class="text-danger">*optional</small></label>
                                                <input type="text"
                                                    class="form-control @error('ukuran') is-invalid @enderror"
                                                    name="ukuran" id="ukuran" placeholder="Tulis ukuran/cc barang"
                                                    aria-label="ukuran">
                                                @error('ukuran')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Bahan</label>
                                                <input type="text"
                                                    class="form-control @error('bahan') is-invalid @enderror"
                                                    name="bahan" id="bahan" required
                                                    placeholder="Tulis bahan barang" aria-label="bahan">
                                                @error('bahan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-6 col-md-4">
                                                <label>Tahun Pembelian</label>
                                                <input type="numeric"
                                                    class="form-control @error('tahun_pembelian') is-invalid @enderror"
                                                    name="tahun_pembelian" id="tahun_pembelian_edit" required
                                                    placeholder="{{ date('Y') }}" aria-label="tahun_pembelian">
                                                @error('tahun_pembelian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Pabrik <small class="text-danger">*optional</small></label>
                                                <input type="text"
                                                    class="form-control @error('pabrik') is-invalid @enderror"
                                                    name="pabrik" id="pabrik" placeholder="Tulis pabrik barang"
                                                    aria-label="pabrik">
                                                @error('pabrik')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Nomor Barang</h6>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Rangka <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('rangka') is-invalid @enderror"
                                            name="rangka" id="rangka" placeholder="Tulis nomor rangka barang"
                                            aria-label="rangka">
                                        @error('rangka')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Mesin <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('mesin') is-invalid @enderror"
                                            name="mesin" id="mesin" placeholder="Tulis nomor mesin barang"
                                            aria-label="mesin">
                                        @error('mesin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Polisi <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('polisi') is-invalid @enderror"
                                            name="polisi" id="polisi" placeholder="Tulis nomor polisi barang"
                                            aria-label="polisi">
                                        @error('polisi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>BPKB <small class="text-danger">*optional</small></label>
                                        <input type="text" class="form-control @error('bpkb') is-invalid @enderror"
                                            name="bpkb" id="bpkb" placeholder="Tulis nomor BPKB barang"
                                            aria-label="bpkb">
                                        @error('bpkb')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="horizontal dark my-3">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h6>PIC</h6>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>NIP</label>
                                        <input type="text"
                                            class="form-control muted @error('nip_pic') is-invalid @enderror"
                                            name="nip_pic" id="nip_pic" readonly placeholder="Tulis NIP PIC Barang"
                                            aria-label="nip_pic">
                                        @error('nip_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Nama</label>
                                        <input type="text"
                                            class="form-control muted @error('nama_pic') is-invalid @enderror"
                                            name="nama_pic" id="nama_pic" readonly placeholder="Tulis Nama PIC Barang"
                                            aria-label="nama_pic">
                                        @error('nama_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Jabatan</label>
                                        <input type="text"
                                            class="form-control muted @error('jabatan_pic') is-invalid @enderror"
                                            name="jabatan_pic" id="jabatan_pic" readonly
                                            placeholder="Tulis Jabatan PIC Barang" aria-label="jabatan_pic">
                                        @error('jabatan_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>No. Telepon</label>
                                        <input type="text"
                                            class="form-control muted @error('telp_pic') is-invalid @enderror"
                                            name="telp_pic" id="telp_pic" readonly
                                            placeholder="Tulis nomor telepon PIC Barang" aria-label="nip_pic">
                                        @error('telp_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Perawatan Barang</h6>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Asal-usul Perolehan</label>
                                        <input type="text"
                                            class="form-control @error('asal_perolehan') is-invalid @enderror"
                                            name="asal_perolehan" id="asal_perolehan"
                                            placeholder="Tulis asal-usul perolehan barang" aria-label="asal_perolehan"
                                            required>
                                        @error('asal_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Nilai Perolehan</label>
                                        <input type="text" name="nilai_perolehan" id="nilai_perolehan"
                                            class="form-control price @error('nilai_perolehan') is-invalid @enderror"
                                            placeholder="Tulis nilai perolehan barang" aria-label="nilai_perolehan"
                                            required>
                                        @error('nilai_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Kondisi</label>
                                        <select name="kondisi" id="kondisi"
                                            class="form-select kondisi @error('kondisi') is-invalid @enderror" required>
                                            <option></option>
                                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>
                                                Baik
                                            </option>
                                            <option value="Perlu Perbaikan"
                                                {{ old('kondisi') == 'Perlu Perbaikan' ? 'selected' : '' }}>
                                                Perlu Perbaikan
                                            </option>
                                            <option value="Rusak Ringan"
                                                {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>
                                                Rusak Ringan
                                            </option>
                                            <option value="Rusak Sedang"
                                                {{ old('kondisi') == 'Rusak Sedang' ? 'selected' : '' }}>
                                                Rusak Sedang
                                            </option>
                                            <option value="Rusak Berat"
                                                {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>
                                                Rusak Berat
                                            </option>
                                        </select>

                                        @error('kondisi')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Tanggal Perawatan</label>
                                        <input type="date"
                                            class="form-control @error('tanggal_perawatan') is-invalid @enderror"
                                            name="tanggal_perawatan" id="tanggal_perawatan_edit"
                                            aria-label="tanggal_perawatan" required>
                                        @error('tanggal_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Harga Perawatan</label>
                                        <input type="text" name="harga_perawatan" id="harga_perawatan"
                                            class="form-control price @error('harga_perawatan') is-invalid @enderror"
                                            placeholder="Tulis harga perawatan barang" aria-label="harga_perawatan"
                                            required>
                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label>Jangka Waktu Perawatan</label>
                                        <select name="waktu_perawatan" id="waktu_perawatan_edit"
                                            class="form-control waktu_perawatan @error('waktu_perawatan') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Jangka Waktu</option>
                                            @for ($i = 1; $i <= 48; $i++)
                                                <option value="{{ $i }}"
                                                    {{ old('waktu_perawatan') == $i ? 'selected' : '' }}>
                                                    {{ $i }} Minggu
                                                </option>
                                            @endfor
                                        </select>

                                        @error('waktu_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Lokasi</h6>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label>Kecamatan</label>
                                        <input type="text" class="form-control"
                                            value="" id="kecamatan_edit" disabled>

                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <input type="hidden" value="" id="sekolah_edit" name="sekolah_id">
                                        <label>Tempat</label>
                                        <select id="sekolah_edit_select"
                                            class="form-control muted place @error('sekolah_id') is-invalid @enderror"
                                            readonly disabled>
                                            <option value=""></option>
                                            @foreach ($places as $place)
                                                <option value="{{ $place->id }}">{{ $place->category . ' ' . $place->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('sekolah_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Gedung</label>
                                        <input type="text"
                                            class="form-control muted @error('gedung') is-invalid @enderror"
                                            name="gedung" id="gedung" placeholder="Tulis lokasi gedung"
                                            aria-label="gedung" readonly>
                                        @error('gedung')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Lantai</label>
                                        <input type="text"
                                            class="form-control muted @error('lantai') is-invalid @enderror"
                                            name="lantai" id="lantai" placeholder="Tulis lokasi lantai"
                                            aria-label="lantai" readonly>
                                        @error('lantai')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Ruangan</label>
                                        <input type="text"
                                            class="form-control muted @error('ruangan') is-invalid @enderror"
                                            name="ruangan" id="ruangan" placeholder="Tulis lokasi ruangan"
                                            aria-label="ruangan" readonly>
                                        @error('ruangan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Detail</label>
                                        <input type="text"
                                            class="form-control muted @error('detail') is-invalid @enderror"
                                            name="detail" id="detail" placeholder="Tulis lokasi detail"
                                            aria-label="detail" readonly>
                                        @error('detail')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-2">
                                <button type="submit" class="btn btn-primary rounded-partner m-0">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show Aset -->
    <div class="modal fade" id="showAset" aria-labelledby="showAsetLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h4 class="text-primary text-gradient">Show <strong>Aset</strong></h4>
                        </div>
                        <div class="card-body mb-3">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h6>Informasi Barang</h6>
                                </div>

                                <!-- Gambar -->
                                <div class="col-4">
                                    <label>Foto Awal:</label>
                                    <div id="preview" style="margin-top: 15px;">
                                        <img id="previewImg" src="" alt="Preview"
                                            style="display: none; max-width: 100%; height: auto;" />
                                    </div>
                                </div>

                                <!-- Informasi -->
                                <div class="col-8">
                                    <div class="row">

                                        <div class="col-12 col-md-6">
                                            <label>Nomor RFID</label>
                                            <input type="text" class="form-control muted" id="tag_show" readonly>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label>Kode Barang</label>
                                            <input type="text" class="form-control muted" id="kode" readonly>
                                        </div>
                                        <div class="col-12">
                                            <label>Nama/Jenis Barang</label>
                                            <input type="text" class="form-control muted" id="name" readonly>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Nomor Register</label>
                                            <input type="text" class="form-control muted" id="register" readonly>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Merk</label>
                                            <input type="text" class="form-control muted" id="merk" readonly>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Ukuran</label>
                                            <input type="text" class="form-control muted" id="ukuran" readonly>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Bahan</label>
                                            <input type="text" class="form-control muted" id="bahan" readonly>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Tahun Pembelian</label>
                                            <input type="numeric"
                                                class="form-control muted @error('tahun_pembelian') is-invalid @enderror"
                                                placeholder="{{ date('Y') }}" name="tahun_pembelian"
                                                id="tahun_pembelian_show" aria-label="tahun_pembelian" required>
                                            @error('tahun_pembelian')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label>Pabrik</label>
                                            <input type="text" class="form-control muted" id="pabrik" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark my-3">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h6>Nomor Barang</h6>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Rangka</label>
                                    <input type="text" class="form-control muted" id="rangka" readonly>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label>Mesin</label>
                                    <input type="text" class="form-control muted" id="mesin" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Polisi</label>
                                    <input type="text" class="form-control muted" id="polisi" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>BPKB</label>
                                    <input type="text" class="form-control muted" id="bpkb" readonly>
                                </div>
                            </div>

                            <hr class="horizontal dark my-3">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h6>PIC</h6>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>NIP</label>
                                    <input type="text" class="form-control muted" id="nip_pic" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Nama</label>
                                    <input type="text" class="form-control muted" id="nama_pic" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Jabatan</label>
                                    <input type="text" class="form-control muted" id="jabatan_pic" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>No. Telepon</label>
                                    <input type="text" class="form-control muted" id="telp_pic" readonly>
                                </div>
                            </div>

                            <hr class="horizontal dark my-3">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h6>Perawatan Barang</h6>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Asal-usul Perolehan</label>
                                    <input type="text" class="form-control muted" id="asal_perolehan" readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Nilai Perolehan</label>
                                    <input type="text" class="form-control price muted" id="nilai_perolehan" readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Kondisi</label>
                                    <select id="kondisi" class="form-select kondisi muted" disabled readonly>
                                        <option value="">Pilih Kondisi</option>
                                        <option value="Baik">Baik</option>
                                        <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                        <option value="Rusak Sedang">Rusak Sedang</option>
                                        <option value="Rusak Berat">Rusak Berat</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Tanggal Perawatan</label>
                                    <input type="date" class="form-control muted" id="tanggal_perawatan_show"
                                        readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Harga Perawatan</label>
                                    <input type="text" class="form-control muted price" id="harga_perawatan" readonly>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Jangka Waktu Perawatan</label>
                                    <select id="waktu_perawatan_show" class="form-control waktu_perawatan muted" disabled
                                        readonly>
                                        <option value="">Pilih Jangka Waktu</option>
                                        @for ($i = 1; $i <= 48; $i++)
                                            <option value="{{ $i }}">{{ $i }} Minggu</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <hr class="horizontal dark my-3">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h6>Lokasi</h6>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label>Kecamatan</label>
                                    <input type="text" class="form-control"
                                        value="" id="kecamatan_show" disabled>

                                    @error('harga_perawatan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="sekolah_show">Tempat</label>
                                    <input type="text" class="form-control muted" id="sekolah_show" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Gedung</label>
                                    <input type="text" class="form-control muted" id="gedung" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Lantai</label>
                                    <input type="text" class="form-control muted" id="lantai" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Ruangan</label>
                                    <input type="text" class="form-control muted" id="ruangan" readonly>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label>Detail</label>
                                    <input type="text" class="form-control muted" id="detail" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Import From Excel --}}
    <div class="modal fade" id="showImportModal" aria-labelledby="showImportModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <div class="card-header pb-0 text-left">
                            <h4 class="text-primary text-gradient">Import <strong>Data Aset</strong></h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="d-flex align-items-center gap-2">
                                        <span>
                                            Tag Tersedia:
                                        </span>
                                        <a href="{{ route('tag.index') }}"
                                            class="badge badge-sm bg-gradient-primary mb-0" target="_blank"
                                            rel="noopener" title="Lihat List Tag">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ $availableTags['firstTagAvailable'] }}" style="text-align: center"
                                            disabled>
                                        <span class="input-group-text">s.d.</span>
                                        <input type="text" class="form-control"
                                            value="{{ $availableTags['lastTagAvailable'] }}" style="text-align: center"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="sekolah_id_import">Tempat</label>
                                    <select name="sekolah_id_import" id="sekolah_id_import"
                                        class="form-control place @error('sekolah_id_import') is-invalid @enderror"
                                        @if (!$availableTags['firstTagAvailable']) {{ 'disabled ' }} @endif required>
                                        <option value="" selected disabled hidden>
                                        </option>
                                        @foreach ($places as $place)
                                            <option value="{{ $place->id }}">
                                                {{ $place->category . ' ' . $place->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sekolah_id_import')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="formImportExcel" class="form-label">
                                        Import Data Aset dari File Excel
                                    </label>
                                    <input class="form-control" type="file" name="file" accept=".xlsx"
                                        id="formImportExcel"
                                        @if (!$availableTags['firstTagAvailable']) {{ 'disabled ' }} @endif required>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <button id="buttonDownloadTemplateImport" class="btn btn-m bg-gradient-primary"
                                        type="button">
                                        Download Template
                                    </button>
                                    <button class="btn btn-sm bg-gradient-warning"
                                        @if (!$availableTags['firstTagAvailable']) {{ 'disabled ' }} @endif>
                                        <i class="fa-solid fa-upload"></i> Import
                                    </button>
                                </div>
                                @if (!$availableTags['firstTagAvailable'])
                                    <div class="small text-danger">
                                        *Import tidak bisa dilakukan, karena tag tidak tersedia.
                                    </div>
                                @endif
                            </form>
                        </div>

                        <div class="card-footer text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const roleId = "{{ auth()->user()->role_id }}";

        $(function() {
            const modalAdd = $("#addAset .modal-content");
            const modalEdit = $("#editAset .modal-content");
            const modalShow = $("#showAset .modal-content");
            const modalImport = $("#showImportModal .modal-content");

            // Inisialisasi Select2 untuk modal yang menggunakan select2 (kondisi, waktu_perawatan, place)
            function initSelect2(parent) {
                $('.kondisi, .waktu_perawatan, .place, .tag', parent).select2({
                    placeholder: "Pilih opsi",
                    dropdownParent: parent,
                    width: "100%"
                });
            }

            initSelect2(modalAdd);
            initSelect2(modalEdit);
            initSelect2(modalShow);
            initSelect2(modalImport);

            // Format harga dengan input mask
            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });

            // Preview gambar sebelum upload
            window.previewImage = function(event) {
                const input = event.target;
                const isEdit = event.target.dataset.edit;
                const preview = isEdit == '0' ?
                    document.getElementById('previewImg') :
                    document.getElementById('previewImgEdit');
                const fileName = isEdit == '0' ?
                    document.getElementById('fileName') :
                    document.getElementById('fileNameEdit');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        fileName.innerText = input.files[0].name;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            };

            // DataTables
            let baseColumns = [{
                    data: 'rfid_number',
                    name: 'rfid_number',
                    className: "text-start"
                },
                {
                    data: 'kode',
                    name: 'kode',
                    className: "text-start"
                },
                {
                    data: 'name',
                    name: 'name',
                    className: "text-start"
                },
                {
                    data: 'merk',
                    name: 'merk',
                    className: "text-start"
                },
                {
                    data: 'tahun_pembelian',
                    name: 'tahun_pembelian',
                    className: "text-start"
                },
                {
                    data: 'kondisi_badge',
                    name: 'kondisi',
                    className: "text-start"
                },
                {
                    data: 'sekolah_name',
                    name: 'sekolah_name'
                }
            ];

            // Tambahkan kolom kecamatan jika roleId == 2
            if (roleId == 2) {
                baseColumns.push({
                    data: 'kecamatan_name',
                    name: 'kecamatan_name',
                    className: "text-start"
                });
            }

            baseColumns.push({
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            });

            const table = $('#asetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('asset.index') }}',
                    data: function(d) {
                        d.kondisi = $('#filterKondisi').find(':selected').val();
                        d.tempat = $('#filterTempat').find(':selected').val();
                        d.tahun_pembelian = $('#filterTahun').find(':selected').val();
                    }
                },
                columns: baseColumns,
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').each(function() {
                        new bootstrap.Tooltip(this);
                    });
                }
            });

            // Trigger reload saat filter diubah
            $('#filterKondisi, #filterTempat, #filterTahun').on('change', function() {
                table.ajax.reload();
            });

            // Fungsi untuk mengisi form modal (add, edit, show)
            function fillAssetForm(prefix, response) {
                console.log(response);
                const asset = response.asset;
                const places = response.places;
                const selector = `#${prefix}`;
                const fields = [
                    'kode', 'name', 'register', 'merk', 'ukuran', 'bahan', 'tahun_pembelian',
                    'pabrik', 'rangka', 'mesin', 'polisi', 'bpkb', 'nip_pic', 'nama_pic', 'jabatan_pic',
                    'telp_pic', 'asal_perolehan', 'nilai_perolehan', 'harga_perawatan', 'gedung', 'lantai',
                    'ruangan', 'detail', 'kecamatan', 'sekolah_id'
                ];

                // Isi field biasa
                fields.forEach(field => {
                    $(`${selector} #${field}`).val(asset[field] ?? '');

                    if (prefix === 'editAset') {
                        $(`${selector} #${field}_edit`).val(asset[field] ?? '');
                    }

                    if (prefix === 'showAset') {
                        $(`${selector} #${field}_show`).val(asset[field] ?? '');
                    }
                });

                // Isi RFID
                if (prefix === 'editAset') {
                    $(`${selector} #tag_edit`).val(asset.rfid_number ?? '');
                }
                if (prefix === 'showAset') {
                    $(`${selector} #tag_show`).val(asset.rfid_number ?? '');
                }

                // Isi tanggal pembelian dan perawatan untuk edit
                if (prefix === 'editAset') {
                    let tanggal = asset.tanggal_perawatan ?? '';

                    if (tanggal) {
                        const dateObj = new Date(tanggal);
                        if (!isNaN(dateObj)) {
                            const yyyy = dateObj.getFullYear();
                            const mm = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                            const dd = ('0' + dateObj.getDate()).slice(-2);
                            tanggal = `${yyyy}-${mm}-${dd}`;
                        }
                    }

                    $(`${selector} #tanggal_perawatan_edit`).val(tanggal);
                }

                // Isi tanggal pembelian dan perawatan untuk show
                if (prefix === 'showAset') {
                    let tanggal = asset.tanggal_perawatan ?? '';

                    if (tanggal) {
                        const dateObj = new Date(tanggal);

                        if (!isNaN(dateObj)) {
                            const yearPerawatan = dateObj.getFullYear();
                            const monthPerawatan = ('0' + (dateObj.getMonth() + 1)).slice(-2);
                            const dayPerawatan = ('0' + dateObj.getDate()).slice(-2);
                            tanggal = `${yearPerawatan}-${monthPerawatan}-${dayPerawatan}`;
                        }
                    }

                    $(`${selector} #tanggal_perawatan_show`).val(tanggal);
                }

                // Isi Jangka Waktu Perawatan (select)
                if (prefix === 'editAset') {
                    $(`${selector} #waktu_perawatan_edit`).val(asset.waktu_perawatan ?? '').trigger('change');

                    // Preview gambar
                    const $previewImg = $(`${selector} #previewImgEdit`);

                    if (asset.foto_awal) {
                        $previewImg.attr('src', `${asset.foto_awal}`).show();
                    } else {
                        $previewImg.hide();
                    }

                }
                if (prefix === 'showAset') {
                    $(`${selector} #waktu_perawatan_show`).val(asset.waktu_perawatan ?? '').trigger('change');

                    // Preview gambar
                    const $previewImg = $(`${selector} #previewImg`);

                    if (asset.foto_awal) {
                        $previewImg.attr('src', `${asset.foto_awal}`).show();
                    } else {
                        $previewImg.hide();
                    }
                }

                // Isi kondisi select2
                const $kondisi = $(`${selector} #kondisi`);
                if ($kondisi.length) {
                    $kondisi.val(asset.kondisi ?? '').trigger('change');
                }

                // Isi sekolah select2 jika ada
                let $sekolah = '';
                if (prefix === 'showAset') {
                    $sekolah = $(`${selector} #sekolah_show`);
                    $sekolah.val(asset.sekolah);
                }

                if (prefix === 'editAset') {
                    $sekolah = $(`${selector} #sekolah_edit`);
                    $sekolah.val(asset.sekolah_id).trigger('change');
                    $(`${selector} #sekolah_edit_select`).val(asset.sekolah_id).trigger('change');
                }

                // Isi kecamatan jika ada
                if (asset.sekolah && asset.sekolah.kecamatan) {
                    const $kecamatan = $(`${selector} #kecamatan`);
                    if ($kecamatan.length) {
                        $kecamatan.val(asset.sekolah.kecamatan.nama_kecamatan).trigger('change');
                    }
                }
            }

            // Event klik tombol edit
            $('#asetTable').on('click', '.edit-asset', function() {
                const assetId = $(this).data('asset-id');
                $.get(`/asset/${assetId}/edit`, function(response) {
                    fillAssetForm('editAset', response);
                    $('#form-edit-asset').attr('action', `/asset/${assetId}`);
                    $('#editAset').modal('show');
                }).fail(function() {
                    console.error("Gagal mengambil data untuk edit.");
                });
            });

            // Event klik tombol show
            $('#asetTable').on('click', '.show-asset', function() {
                const assetId = $(this).data('asset-id');
                $.get(`/asset/${assetId}/edit`, function(response) {
                    fillAssetForm('showAset', response);
                    $('#showAset').modal('show');
                }).fail(function() {
                    console.error("Gagal mengambil data untuk lihat.");
                });
            });

            /**
             * Date: 28-04-2025
             */
            // Event klik tombol export
            $('#buttonExport').on('click', function(e) {
                e.preventDefault();

                const kondisi = $('#filterKondisi').find(':selected').val();
                const tempat = $('#filterTempat').find(':selected').val();
                const tahun = $('#filterTahun').find(':selected').val();

                // Buat URL dengan parameter yang tidak kosong
                const params = new URLSearchParams();

                if (kondisi !== '') params.append('kondisi', kondisi);
                if (tempat !== '') params.append('tempat', tempat);
                if (tahun !== '') params.append('tahun', tahun);

                const url = '/export/asset' + (params.toString() ? '?' + params.toString() : '');
                window.location.href = url;
            });

            // Event untuk buka modal import data aset
            $('#buttonShowImportModal').on('click', function() {
                $('#showImportModal').modal('show');
            })
        });

        /**
         * Date: 03-05-2025
         * Download Template untuk Import Data Aset
         **/
        $('#buttonDownloadTemplateImport').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('asset.download.template.import') }}',
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // penting untuk file binary
                },
                success: function(data, status, xhr) {
                    const blob = new Blob([data]);
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'template_import_data_aset.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                },
                error: function(xhr) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        iconColor: 'white',
                        customClass: {
                            popup: 'colored-toast'
                        },
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'File Template untuk Import Data Aset Gagal Diunduh'
                    });
                }
            });
        });
    </script>
@endpush
