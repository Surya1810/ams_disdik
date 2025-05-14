@extends('layouts.app')

@section('title', 'Detail History Stock Opname')

@push('css')
    <style>
        #filterStatus:hover {
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
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">
                Detail History Stock Opname
            </li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">
            Detail History Stock Opname
        </h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div>
            <!-- Tabel Asset -->
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h6 class="text-white text-capitalize ps-3">Detail History Stock Opname</h6>
                                </div>
                                <div class="col-6 text-end ps-3 text-white pe-4">
                                    <small>Found:
                                        <strong id="totalIsThereTrue">{{ $statusCounts['foundCount'] }}</strong>
                                    </small>
                                    <small>Missing:
                                        <strong id="totalIsThereFalse">{{ $statusCounts['missingCount'] }}</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <div class="d-flex flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white border-0">
                                        <i class="fa-solid fa-filter"></i>
                                    </span>
                                    <select id="filterStatus" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="1">FOUND</option>
                                        <option value="0">MISSING</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text muted fw-bold">Kecamatan</span>
                                    <input type="text" class="form-control muted" value="{{ $scan['district_name'] }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text muted fw-bold">Tempat</span>
                                    <input type="text" class="form-control muted" value="{{ $scan['place_name'] }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text muted fw-bold">Waktu Discan:</span>
                                    <input type="text" class="form-control muted"
                                        value="{{ $scan['created_at']->format('Y-m-d H:i') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 mb-3 overflow-x-scroll">
                            <table id="assetTable" class="table text-sm mt-3">
                                <thead class="font-weight-bolder">
                                    <tr>
                                        <th class="text-uppercase">Status</th>
                                        <th class="text-uppercase">RFID</th>
                                        <th class="text-uppercase">Kode Barang</th>
                                        <th class="text-uppercase">Nama/Jenis Barang</th>
                                        <th class="text-uppercase">Merk/Type</th>
                                        <th class="text-uppercase">Gedung</th>
                                        <th class="text-uppercase">Lantai</th>
                                        <th class="text-uppercase">Ruangan</th>
                                        <th class="text-uppercase">Kondisi</th>
                                        <th class="text-uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data Asset -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 mb-3 text-center">
                        <button class="btn btn-primary rounded-partner" id="exportFound" data-scan-id="{{ $scan->id }}">
                            <i class="fa-solid fa-download"></i> Export Found
                        </button>
                        <button class="btn btn-primary rounded-partner" id="exportMissing" data-scan-id="{{ $scan->id }}">
                            <i class="fa-solid fa-download"></i> Export Missing
                        </button>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Show Aset -->
    <div class="modal fade" id="showAset" aria-labelledby="showAsetLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
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
                                            <input type="text" class="form-control muted" id="tahun_pembelian"
                                                readonly>
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
                                        <option value="3">3 Bulan</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12">12 Bulan</option>
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
                                        value="{{ Auth::user()->kecamatan->name }}" disabled>

                                    @error('harga_perawatan')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="placeName">Tempat</label>
                                    <input id="placeName" type="text" class="form-control muted" readonly>
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
@endsection

@push('scripts')
    <script type='text/javascript'>
        const table = $('#assetTable').DataTable({
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50],
                [10, 25, 50]
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('scanned.detail', $scan['id']) }}',
                data: function(d) {
                    d.is_there = $('#filterStatus').find(':selected').val();
                }
            },
            rowCallback: function(row, data, index) {
                // Cek isi kolom 'is_there' (karena pakai raw HTML <strong>)
                const isFound = data.is_there.includes('FOUND');

                if (isFound) {
                    $(row).removeClass('table-danger').addClass('table-success');
                } else {
                    $(row).removeClass('table-success').addClass('table-danger');
                }
            },
            columns: [{
                    data: 'is_there',
                    name: 'is_there',
                    className: 'text-start'
                },
                {
                    data: 'rfid_number',
                    name: 'rfid_number',
                    className: "text-start",
                },
                {
                    data: 'kode',
                    name: 'kode',
                    className: "text-start",
                    orderable: false
                },
                {
                    data: 'name',
                    name: 'name',
                    className: "text-start",
                    orderable: false
                },
                {
                    data: 'merk',
                    name: 'merk',
                    className: "text-start",
                    orderable: false
                },
                {
                    data: 'gedung',
                    name: 'gedung',
                    className: 'text-start',
                    orderable: false
                },
                {
                    data: 'lantai',
                    name: 'lantai',
                    className: 'text-start',
                    orderable: false
                },
                {
                    data: 'ruangan',
                    name: 'ruangan',
                    className: 'text-start',
                    orderable: false
                },
                {
                    data: 'kondisi',
                    name: 'kondisi',
                    className: 'text-start',
                    orderable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-start',
                    orderable: false,
                    searchable: false
                }
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function() {
                    new bootstrap.Tooltip(this);
                });
            }
        });

        // Tombol untuk mengekspor data Found
        document.getElementById('exportFound').addEventListener('click', function () {
        const scanId = this.getAttribute('data-scan-id');
        window.location.href = `/scan/export-found/${scanId}`;
        });
        
        document.getElementById('exportMissing').addEventListener('click', function () {
        const scanId = this.getAttribute('data-scan-id');
        window.location.href = `/scan/export-missing/${scanId}`;
        });

        $('#filterStatus').on('change', function() {
            table.ajax.reload();
        });

        // Buka modal show aset
        function buttonModalShowAset(element) {
            const rfid = element.dataset.rfid;

            $.get(`/scan/assets/${rfid}/json`, function(response) {
                fillAssetForm('showAset', response.asset);
                $('#showAset').modal('show');
            }).fail(function() {
                console.error("Gagal mengambil data untuk lihat.");
            });
        }

        // Fungsi untuk mengisi form modal (add, edit, show)
        function fillAssetForm(prefix, asset) {
            const selector = `#${prefix}`;
            const fields = [
                'kode', 'name', 'register', 'merk', 'ukuran', 'bahan', 'tahun_pembelian',
                'pabrik', 'rangka', 'mesin', 'polisi', 'bpkb', 'nip_pic', 'nama_pic', 'jabatan_pic',
                'telp_pic', 'asal_perolehan', 'nilai_perolehan', 'harga_perawatan', 'gedung', 'lantai',
                'ruangan', 'detail'
            ];

            // Isi field biasa
            fields.forEach(field => {
                $(`${selector} #${field}`).val(asset[field] ?? '');
            });

            if (prefix === 'showAset') {
                $(`${selector} #tag_show`).val(asset.rfid_number ?? '');
            }

            if (prefix === 'showAset') {
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
                $(`${selector} #tanggal_perawatan_show`).val(tanggal);
            }

            if (prefix === 'showAset') {
                $(`${selector} #waktu_perawatan_show`).val(asset.waktu_perawatan ?? '').trigger('change');
            }

            // Isi kondisi select2
            const $kondisi = $(`${selector} #kondisi`);
            if ($kondisi.length) {
                $kondisi.val(asset.kondisi ?? '').trigger('change');
            }

            const $sekolah = $(`${selector} #placeName`);
            $($sekolah).val(asset.sekolah.category + ' ' + asset.sekolah.name);

            // Isi kecamatan jika ada
            if (asset.sekolah && asset.sekolah.kecamatan) {
                const $kecamatan = $(`${selector} #kecamatan`);
                if ($kecamatan.length) {
                    $kecamatan.val(asset.sekolah.kecamatan.nama_kecamatan).trigger('change');
                }
            }

            // Preview gambar
            const $previewImg = $(`${selector} #previewImg`);

            if (asset.foto_awal) {
                $previewImg.attr('src', `${asset.foto_awal}`).show();
            } else {
                $previewImg.hide();
            }
        }
    </script>
@endpush
