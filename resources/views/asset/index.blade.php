@extends('layouts.app')

@section('title')
    Aset
@endsection

@push('css')
@endpush

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm">
                <a class="text-white opacity-5" href="javascript:;">Halaman</a>
            </li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Aset</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Aset</h6>
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
                            <h6 class="text-white text-capitalize ps-3">List Aset</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <button type="button" class="btn bg-gradient-primary rounded-partner" data-bs-toggle="modal"
                            data-bs-target="#addAset"> <i class="fa-solid fa-plus"></i> Tambah
                        </button>
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
                                    <th class="text-uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $asset)
                                    <tr>
                                        <td>{{ $asset->rfid }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Aset -->
    <div class="modal fade" id="addAset" aria-labelledby="addAset" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="card card-plain">
                        <form action="{{ route('asset.store') }}" method="POST">
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

                                    </div>
                                    <div class="col-8">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <label>RFID <small class="text-danger">*Wajib</small></label>

                                                @error('tag')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label>Kode Barang <small class="text-danger">*Wajib</small></label>
                                                <input type="text"
                                                    class="form-control @error('kode') is-invalid @enderror" name="kode"
                                                    value="{{ old('kode') }}" required placeholder="Tulis kode barang"
                                                    aria-label="kode">
                                                @error('kode')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label>Nama/Jenis Barang <small class="text-danger">*Wajib</small></label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ old('name') }}" required
                                                    placeholder="Tulis nama/jenis barang" aria-label="name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Nomor Register <small class="text-danger">*Wajib</small></label>
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
                                                <label>Merk/Type <small class="text-danger">*Wajib</small></label>
                                                <input type="text"
                                                    class="form-control @error('merk') is-invalid @enderror" name="merk"
                                                    value="{{ old('merk') }}" required
                                                    placeholder="Tulis merk/type barang" aria-label="merk">
                                                @error('merk')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Ukuran/cc</label>
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
                                                    placeholder="Tulis bahan barang" aria-label="bahan">
                                                @error('bahan')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-6 col-md-4">
                                                <label>Tahun Pembelian <small class="text-danger">*Wajib</small></label>
                                                <input type="year"
                                                    class="form-control @error('tahun_pembelian') is-invalid @enderror"
                                                    name="number" value="{{ old('tahun_pembelian') }}"
                                                    placeholder="{{ Carbon\Carbon::now()->year }}" min="1900"
                                                    max="2099" step="1" aria-label="tahun_pembelian"
                                                    value="{{ Carbon\Carbon::now()->year }}" required>
                                                @error('tahun_pembelian')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <label>Pabrik</label>
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
                                        <label>Rangka</label>
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
                                        <label>Mesin</label>
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
                                        <label>Polisi</label>
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
                                        <label>BPKB</label>
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
                                            placeholder="Tulis NIP PIC barang" aria-label="nip_pic">
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
                                            class="form-control @error('jabatan_pic') is-invalid @enderror"
                                            name="jabatan_pic" value="{{ old('jabatan_pic') }}"
                                            placeholder="Tulis jabatan PIC barang" aria-label="jabatan_pic">
                                        @error('jabatan_pic')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Telepon</label>
                                        <input type="text"
                                            class="form-control @error('telp_pic') is-invalid @enderror" name="telp_pic"
                                            value="{{ old('telp_pic') }}" placeholder="Tulis nomor telepon PIC barang"
                                            aria-label="telp_pic">
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
                                            placeholder="Tulis asal-usul perolehan barang" aria-label="asal_perolehan">
                                        @error('asal_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Nilai Perolehan</label>
                                        <input type="text"
                                            class="form-control @error('nilai_perolehan') is-invalid @enderror"
                                            name="nilai_perolehan" value="{{ old('nilai_perolehan') }}"
                                            placeholder="Tulis nilai perolehan barang" aria-label="nilai_perolehan">
                                        @error('nilai_perolehan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Kondisi</label>

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
                                            aria-label="tanggal_perawatan">
                                        @error('tanggal_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Harga Perawatan</label>
                                        <input type="text"
                                            class="form-control @error('harga_perawatan') is-invalid @enderror"
                                            name="harga_perawatan" value="{{ old('harga_perawatan') }}"
                                            placeholder="Tulis harga perawatan barang" aria-label="harga_perawatan">
                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label>Jangka Waktu Perawatan</label>

                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <hr class="horizontal dark my-3">
                                    <div class="col-12 text-center">
                                        <h6>Lokasi</h6>
                                    </div>
                                    <div class="col-12">
                                        <label>Tempat</label>

                                        @error('harga_perawatan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label>Gedung</label>
                                        <input type="text" class="form-control @error('gedung') is-invalid @enderror"
                                            name="gedung" value="{{ old('gedung') }}" placeholder="Tulis lokasi gedung"
                                            aria-label="gedung">
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
                                            aria-label="lantai">
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
                                            placeholder="Tulis lokasi ruangan" aria-label="ruangan">
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
                                            aria-label="detail">
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
@endsection

@push('scripts')
@endpush
