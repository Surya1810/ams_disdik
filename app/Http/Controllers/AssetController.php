<?php

namespace App\Http\Controllers;

// Export and Import
use App\Imports\AssetsImport;
use App\Exports\AssetsExport;

use App\Models\Asset;
use App\Models\Sekolah;
use App\Models\Tag;
use App\Models\History;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon as CarbonCarbon;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil kecamatan_id dari user yang sedang login
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        // Ambil tag yang statusnya 'available' dan sesuai dengan kecamatan user
        $tags = Tag::where('status', 'available')
            ->where('kecamatan_id', $kecamatanId)
            ->pluck('rfid_number');

        // Ambil data sekolah yang sesuai dengan kecamatan user
        if ($roleId == 2) {
            $placesForFilter = Kecamatan::whereNot('id', 1)->get();
            $places = Sekolah::where('kecamatan_id', $kecamatanId)->get();
        } else if ($roleId == 3) {
            $places = Sekolah::where('kecamatan_id', $kecamatanId)->get();
        }

        // Untuk role 2 dan 3, hanya dapat melihat aset di kecamatan dan sekolah atau kantor mereka
        $asset = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
            $query->where('kecamatan_id', $kecamatanId);
        })->with('sekolah')->get();

        if ($request->ajax()) {
            // Query untuk DataTables
            $assetsQuery = Asset::with('sekolah.kecamatan');

            // Filter berdasarkan kecamatan untuk role 2 dan 3
            if ($roleId != 1) {
                $assetsQuery->whereHas('sekolah', function ($query) use ($kecamatanId, $roleId) {
                    if ($roleId == 3) {
                        $query->where('kecamatan_id', $kecamatanId);
                    }
                });
            }

            // Filter
            if ($request->filled('kondisi')) {
                $assetsQuery->where('kondisi', $request->kondisi);
            }

            if ($request->filled('tempat')) {
                if ($roleId == 2) {
                    $assetsQuery = Asset::whereHas('sekolah.kecamatan', function ($query) use ($request) {
                        $query->where('id', $request->tempat);
                    });
                } else {
                    $assetsQuery->where('sekolah_id', $request->tempat);
                }
            }

            if ($request->filled('tahun_pembelian')) {
                $assetsQuery->where('tahun_pembelian', $request->tahun_pembelian);
            }

            if ($roleId == 2) {
                $dataTable = DataTables::of($assetsQuery)
                    ->addColumn('sekolah_name', function ($row) {
                        return $row->sekolah->category . ' ' . $row->sekolah->name;
                    })
                    ->addColumn('kecamatan_name', function ($row) {
                        return optional($row->sekolah->kecamatan)->name ?? '-';
                    })
                    ->addColumn('kondisi_badge', function ($row) {
                        $badge = match ($row->kondisi) {
                            'Baik' => '<span class="badge bg-success">Baik</span>',
                            'Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang' => '<span class="badge bg-warning">' . $row->kondisi . '</span>',
                            'Rusak Berat' => '<span class="badge bg-danger">' . $row->kondisi . '</span>',
                            default => '<span class="badge bg-secondary">' . $row->kondisi . '</span>',
                        };
                        return $badge;
                    })
                    ->addColumn('action', function ($row) {
                        $showButton = '<a href="javascript:void(0)" class="btn btn-link p-0 show-asset" data-asset-id="' . $row->id . '">
                            <i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail"></i>
                        </a>';

                        $editButton = '&nbsp;
                            <a href="javascript:void(0)" class="btn btn-link p-0 edit-asset" data-asset-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"></i>
                            </a>';

                        $downloadButton = '&nbsp;
                            <a href="' . route('asset.download', $row->id) . '" class="btn btn-link p-0 download-pdf">
                                <i class="fa-solid fa-file-pdf text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Unduh PDF"></i>
                            </a>';

                        $assetKecamatanId = $row->load('sekolah')->sekolah->kecamatan_id;

                        $buttons = Auth::user()->kecamatan_id != $assetKecamatanId
                            ? $showButton . $downloadButton
                            : $showButton . $editButton . $downloadButton;

                        return $buttons;
                    })
                    ->rawColumns(['kondisi_badge', 'action']);
            } else {
                $dataTable = DataTables::of($assetsQuery)
                    ->addColumn('sekolah_name', function ($row) {
                        return $row->sekolah->category . ' ' . $row->sekolah->name;
                    })
                    ->addColumn('kondisi_badge', function ($row) {
                        $badge = match ($row->kondisi) {
                            'Baik' => '<span class="badge bg-success">Baik</span>',
                            'Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang' => '<span class="badge bg-warning">' . $row->kondisi . '</span>',
                            'Rusak Berat' => '<span class="badge bg-danger">' . $row->kondisi . '</span>',
                            default => '<span class="badge bg-secondary">' . $row->kondisi . '</span>',
                        };
                        return $badge;
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="javascript:void(0)" class="btn btn-link p-0 show-asset" data-asset-id="' . $row->id . '">
                                <i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail"></i>
                            </a>
                            &nbsp;
                            <a href="javascript:void(0)" class="btn btn-link p-0 edit-asset" data-asset-id="' . $row->id . '">
                                <i class="fa-solid fa-pencil" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"></i>
                            </a>
                            <a href="' . route('asset.download', $row->id) . '" class="btn btn-link p-0 download-pdf">
                                <i class="fa-solid fa-file-pdf text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Unduh PDF"></i>
                            </a>';
                    })
                    ->rawColumns(['kondisi_badge', 'action']);
            }

            return $dataTable->make(true);
        }

        // Untuk pilihan di filter tahun pembelian
        $tahunPembelianArr = $asset->pluck('tahun_pembelian')->unique()->values()->all();

        // Get available tag in range
        $firstTagAvailable = Tag::where('status', 'available')
            ->where('kecamatan_id', $kecamatanId)
            ->orderBy('rfid_number', 'ASC')
            ->value('rfid_number');
        $lastTagAvailable = Tag::where('status', 'available')
            ->where('kecamatan_id', $kecamatanId)
            ->orderBy('rfid_number', 'DESC')
            ->value('rfid_number');
        $availableTags = [
            'firstTagAvailable' => $firstTagAvailable,
            'lastTagAvailable' => $lastTagAvailable
        ];

        $compactedData = isset($placesForFilter)
            ? compact('tags', 'places', 'asset', 'tahunPembelianArr', 'availableTags', 'placesForFilter')
            : compact('tags', 'places', 'asset', 'tahunPembelianArr', 'availableTags');

        return view('asset.index', $compactedData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tag' => 'required|exists:tags,rfid_number',
            'sekolah_id' => 'required',
            'kode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'register' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'bahan' => 'required|string|max:255',
            'tahun_pembelian' => 'required|integer',
            'nip_pic' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'jabatan_pic' => 'required|string|max:255',
            'telp_pic' => 'required|min:10',
            'asal_perolehan' => 'required|string|max:255',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required',
            'tanggal_perawatan' => 'required|date',
            'harga_perawatan' => 'required|numeric|min:0',
            'waktu_perawatan' => 'required|numeric|min:0',
            'gedung' => 'required',
            'lantai' => 'required',
            'ruangan' => 'required',
            'detail' => 'required',
            'ukuran' => 'nullable',
            'pabrik' => 'nullable',
            'rangka' => 'nullable',
            'mesin' => 'nullable',
            'polisi' => 'nullable',
            'bpkb' => 'nullable'
        ]);

        try {
            $validated['rfid_number'] = $validated['tag'];
            unset($validated['tag']);

            // Upload gambar
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename = Str::uuid() . '.webp';
                $path = 'assets/' . $filename;

                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('image')->getPathname())
                    ->scale(width: 800)
                    ->toWebp(quality: 75);

                Storage::disk('public')->put($path, (string) $image);
                $validated['foto_awal'] = $filename;
            }

            // Simpan asset
            $asset = Asset::create($validated);

            // Update status tag jadi used
            Tag::where('rfid_number', $asset->rfid_number)->update(['status' => 'used']);

            return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil ditambahkan', 'level-alert' => 'alert-success']);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan saat menambahkan aset: ' . $e->getMessage(),
                'level-alert' => 'alert-danger',
            ]);
        }
    }

    public function update(Request $request, Asset $asset)
    {
        // cegah user selain pemiliknya agar tidak bisa update asset
        $kecamatanId = $asset->load('sekolah')->sekolah->kecamatan_id;

        if (Auth::user()->kecamatan_id != $kecamatanId) {
            return redirect()
                ->route('asset.index')
                ->with([
                    'pesan' => 'Hanya pemilik/kecamatan terkait yang dapat mengubah data aset',
                    'level-alert' => 'alert-warning'
                ]);
        }

        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tag' => 'required|exists:tags,rfid_number',
            'sekolah_id' => 'required',
            'kode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'register' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'bahan' => 'required|string|max:255',
            'tahun_pembelian' => 'required|integer',
            'nip_pic' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'jabatan_pic' => 'required|string|max:255',
            'telp_pic' => 'required|min:10',
            'asal_perolehan' => 'required|string|max:255',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required',
            'tanggal_perawatan' => 'required|date',
            'harga_perawatan' => 'required|numeric|min:0',
            'waktu_perawatan' => 'required|numeric|min:0',
            'gedung' => 'required',
            'lantai' => 'required',
            'ruangan' => 'required',
            'detail' => 'required',
            'ukuran' => 'nullable',
            'pabrik' => 'nullable',
            'rangka' => 'nullable',
            'mesin' => 'nullable',
            'polisi' => 'nullable',
            'bpkb' => 'nullable'
        ]);

        try {
            $oldValues = $asset->toArray(); // ambil semua data lama

            // Update tag status jika berubah
            if ($validated['tag'] !== $asset->rfid_number) {
                Tag::where('rfid_number', $asset->rfid_number)->update(['status' => 'available']);
                Tag::where('rfid_number', $validated['tag'])->update(['status' => 'used']);
            }

            $validated['rfid_number'] = $validated['tag'];
            unset($validated['tag']);

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename = Str::uuid() . '.webp';
                $path = 'assets/' . $filename;

                $manager = new ImageManager(new Driver());
                $image = $manager->read($request->file('image')->getPathname())
                    ->scale(width: 800)
                    ->toWebp(quality: 75);

                Storage::disk('public')->put($path, (string) $image);

                if ($asset->foto_awal && Storage::disk('public')->exists('assets/' . $asset->foto_awal)) {
                    Storage::disk('public')->delete('assets/' . $asset->foto_awal);
                }

                $validated['foto_awal'] = $filename;
            }

            $asset->update($validated);
            $newValues = $asset->fresh()->toArray(); // ambil data terbaru
            $changedFields = [];

            foreach ($newValues as $key => $new) {
                $old = $oldValues[$key] ?? null;

                if (is_array($old) || is_array($new)) {
                    if (json_encode($old) !== json_encode($new)) {
                        $changedFields[] = $key;
                    }
                } elseif ((string) $old !== (string) $new) {
                    $changedFields[] = $key;
                }
            }

            History::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'change_type' => 'attribute',
                'requester_id' => Auth::user()->kecamatan_id,
                'requester_payload' => json_encode([
                    'id' => Auth::id(),
                    'name' => Auth::user()->name
                ], true),
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($newValues),
                'changed_fields' => json_encode($changedFields),
                'old_asset' => json_encode($asset)
            ]);

            return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil diperbarui', 'level-alert' => 'alert-success']);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan saat memperbarui aset: ' . $e->getMessage(),
                'level-alert' => 'alert-danger',
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $tags = Tag::where('status', 'available')
            ->orWhere('rfid_number', $asset->rfid_number)
            ->where('kecamatan_id', Auth::user()->kecamatan_id)
            ->pluck('rfid_number');

        $places = Sekolah::where('kecamatan_id', Auth::user()->kecamatan_id)->get();
        $asset->foto_awal = (!$asset->foto_awal || $asset->foto_awal === 'dummy.jpg')
            ? asset('assets/Image/no_image.png')
            : asset(Storage::url('public/assets/' . $asset->foto_awal));

        return response()->json([
            'asset' => $asset,
            'tags' => $tags,
            'places' => $places,
        ]);
    }

    public function download($id)
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $asset = Asset::findOrFail($id);

        $asset->foto_awal = (!$asset->foto_awal || $asset->foto_awal === 'dummy.jpg')
            ? asset('assets/Image/no_image.png')
            : Storage::url('public/assets/' . $asset->foto_awal);

        $places = Sekolah::where('kecamatan_id', $kecamatanId)->get();
        $pdf = Pdf::loadView('asset.download_pdf', compact('asset', 'places'))
            ->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

        return $pdf->download('Detail-Aset-' . $asset->rfid_number . '.pdf');
    }

    public function maintenance(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            $query = \App\Models\Asset::query()
                ->whereIn('kondisi', ['Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat']);

            // Filtering berdasarkan role
            if ($user->role->id == 3) {
                $query->whereHas('sekolah', function ($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                });
            }

            // Filter berdasarkan waktu perawatan (1-48 minggu)
            if ($request->filled('waktu')) {
                $selected = (int) $request->waktu;

                // Konversi ke array rentang minggu
                $allowed = [];
                for ($i = 1; $i <= $selected; $i++) {
                    $allowed[] = $i;
                }

                $query->whereIn('waktu_perawatan', $allowed);
            }

            // Hitung total seluruh harga perawatan aset sesuai filter yang aktif
            $totalSeluruhHarga = (clone $query)->sum('harga_perawatan');

            return DataTables::of($query)
                ->addColumn('checkbox', function ($row) {
                    $tanggalPerawatan = $row->tanggal_perawatan
                        ? \Carbon\Carbon::parse($row->tanggal_perawatan)
                        : null;
                    $hariIni = \Carbon\Carbon::today();

                    if ($tanggalPerawatan && $tanggalPerawatan->lessThanOrEqualTo($hariIni)) {
                        return '<input type="checkbox" class="maintenance-checkbox" data-id="' . $row->id . '" data-waktu="' . (int)$row->waktu_perawatan . '">';
                    }
                    return '';
                })
                ->addColumn('rfid_number', fn($row) => $row->rfid_number)
                ->addColumn('kode', fn($row) => $row->kode)
                ->addColumn('kecamatan', function ($row) use ($user) {
                    if ($user->role->id == 2) {
                        return $row->sekolah && $row->sekolah->kecamatan ? $row->sekolah->kecamatan->name : '-';
                    }
                    return null;
                })
                ->editColumn(
                    'tanggal_perawatan',
                    fn($row) =>
                    $row->tanggal_perawatan
                        ? \Carbon\Carbon::parse($row->tanggal_perawatan)->format('d-m-Y')
                        : '-'
                )
                ->editColumn(
                    'waktu_perawatan',
                    fn($row) =>
                    $row->waktu_perawatan ? $row->waktu_perawatan . ' Minggu' : '-'
                )
                ->setRowClass(function ($row) {
                    $waktuPerawatan = is_numeric($row->waktu_perawatan) ? (int) $row->waktu_perawatan : 0;
                    $jatuhTempo = $row->tanggal_perawatan
                        ? \Carbon\Carbon::parse($row->tanggal_perawatan)->addWeeks($waktuPerawatan)
                        : null;
                    return ($jatuhTempo && $jatuhTempo->isPast()) ? 'table-danger' : '';
                })
                ->with('totalSeluruhHarga', $totalSeluruhHarga)
                ->rawColumns(['checkbox'])
                ->make(true);
        }

        $assets = Asset::all();
        return view('asset.maintenance', compact('assets'));
    }

    public function markAsMaintained(Request $request)
    {
        $request->validate([
            'assets' => 'required|array',
            'assets.*.id' => 'required|exists:assets,id',
            'assets.*.waktu' => 'required|integer|min:1'
        ]);

        try {
            foreach ($request->assets as $assetData) {
                $asset = Asset::find($assetData['id']);

                $waktu = (int) $assetData['waktu'];

                if ($waktu > 0) {
                    $tanggal_perawatan = now()->addWeeks($waktu);
                    $asset->tanggal_perawatan = $tanggal_perawatan;
                }

                $asset->kondisi = 'Baik'; // Tambahan: ubah status menjadi Baik
                $asset->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Tanggal perawatan dan status berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage()
            ], 500);
        }
    }

    public function maintenancePdf(Request $request)
    {
        $user = Auth::user();
        $query = \App\Models\Asset::query()
            ->whereIn('kondisi', ['Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat']);

        if ($user->role_id == 3) {
            $query->whereHas('sekolah', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        }

        if ($request->filled('waktu')) {
            $weeks = (int) $request->waktu;
            $cutoffDate = now()->subWeeks($weeks)->startOfDay();
            $query->whereDate('tanggal_perawatan', '>=', $cutoffDate);
        }

        $maintenanceList = $query->orderBy('tanggal_perawatan', 'desc')->get();

        $pdf = PDF::loadView('asset.maintenance_pdf', compact('maintenanceList'));
        return $pdf->download('berita_acara_barang_rusak.pdf');
    }

    /**
     * Date: 28-04-2025
     * Export List Asset to Excel
     */
    public function export(Request $request)
    {
        $kondisi = $request->query('kondisi');
        $tempat = $request->query('tempat');
        $tahun  = $request->query('tahun');

        $date = date('Y-m-d');
        $fileName = "List Data Aset - $date.xlsx";

        if (Auth::user()->role_id == 2) {
            if (is_null($tempat)) {
                return redirect()
                    ->route('asset.index')
                    ->with([
                        'pesan' => 'Mohon pilih satu kecamatan saja untuk di export!',
                        'level-alert' => 'alert-warning'
                    ]);
            }
        }

        return Excel::download(
            new AssetsExport($kondisi, $tempat, $tahun),
            $fileName
        );
    }

    /**
     * Date: 28-04-2025
     * Import Data Asset from Excel
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx',
            'sekolah_id_import' => 'required|exists:sekolahs,id',
        ]);

        $import = new AssetsImport($validated['sekolah_id_import']);
        Excel::import($import, $request->file('file'));

        if ($import->getErrors()) {
            return redirect()->route('asset.index')->with([
                'pesan' => 'Import data aset tidak sepenuhnya berhasil. Silahkan cek pesan yang muncul',
                'list_errors' => $import->getErrors(),
                'level-alert' => 'alert-warning',
            ]);
        }

        return redirect()->route('asset.index')->with([
            'pesan' => 'Import data aset sepenuhnya telah berhasil!',
            'level-alert' => 'alert-success',
        ]);
    }

    /**
     * Date: 03-05-2025
     * Fungsi untuk download template import data aset
     */
    public function downloadTemplateImport()
    {
        $path = storage_path('app/templates/template_import_data_aset.xlsx');

        if (!file_exists($path)) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        return response()->download($path, 'template_import_data_aset.xlsx');
    }
}
