<?php

namespace App\Http\Controllers;

// Export and Import
use App\Imports\AssetsImport;
use App\Exports\AssetsExport;

use App\Models\Asset;
use App\Models\Sekolah;
use App\Models\Tag;
use App\Models\History;
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
        $places = Sekolah::where('kecamatan_id', $kecamatanId)->get();

        // Untuk role 1, bisa melihat semua aset
        if ($roleId == 1) {
            $asset = Asset::all();
        } else {
            // Untuk role 2 dan 3, hanya dapat melihat aset di kecamatan dan sekolah mereka
            $asset = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })->with('sekolah')->get();
        }

        if ($request->ajax()) {
            // Query untuk DataTables
            $assetsQuery = Asset::with('sekolah');

            // Filter berdasarkan kecamatan untuk role 2 dan 3
            if ($roleId != 1) {
                $assetsQuery->whereHas('sekolah', function ($query) use ($kecamatanId) {
                    $query->where('kecamatan_id', $kecamatanId);
                });
            }

            // Filter
            if ($request->filled('kondisi')) {
                $assetsQuery->where('kondisi', $request->kondisi);
            }

            if ($request->filled('tempat')) {
                $assetsQuery->where('sekolah_id', $request->tempat);
            }

            if ($request->filled('tahun_pembelian')) {
                $assetsQuery->where('tahun_pembelian', $request->tahun_pembelian);
            }

            return DataTables::of($assetsQuery)
                ->addColumn('sekolah_name', function ($row) {
                    return $row->sekolah->category . ' ' . $row->sekolah->name;
                })
                ->addColumn('kondisi_badge', function ($row) {
                    $badge = match ($row->kondisi) {
                        'Baik' => '<span class="badge bg-success">Baik</span>',
                        'Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang' => '<span class="badge bg-warning">' . $row->kondisi . '</span>',
                        'Rusak Berat', 'Hilang' => '<span class="badge bg-danger">' . $row->kondisi . '</span>',
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
            ';
                })
                ->rawColumns(['kondisi_badge', 'action'])
                ->make(true);
        }

        // Untuk pilihan di filter tahun pembelian
        $tahunPembelianArr = $asset->pluck('tahun_pembelian')->unique()->values()->all();

        return view('asset.index', compact('tags', 'places', 'asset', 'tahunPembelianArr'));
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
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode($newValues),
                'changed_fields' => json_encode($changedFields),
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

        return response()->json([
            'asset' => $asset,
            'tags' => $tags,
            'places' => $places,
        ]);
    }

    public function maintenance(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();

            $query = \App\Models\Asset::query()
                ->whereIn('kondisi', ['Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat']);

            if ($user->role != 'admin') {
                $query->whereHas('sekolah', function ($q) use ($user) {
                    $q->where('kecamatan_id', $user->kecamatan_id);
                });
            }

            if ($request->filled('waktu')) {
                $selected = (int) $request->waktu;
                $allowed = [];

                if ($selected === 3) {
                    $allowed = [3];
                } elseif ($selected === 6) {
                    $allowed = [3, 6];
                } elseif ($selected === 12) {
                    $allowed = [3, 6, 12];
                }

                $query->whereIn('waktu_perawatan', $allowed);
            }

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

                ->editColumn('tanggal_perawatan', function ($row) {
                    return $row->tanggal_perawatan
                        ? \Carbon\Carbon::parse($row->tanggal_perawatan)->format('d-m-Y')
                        : '-';
                })
                ->editColumn('waktu_perawatan', function ($row) {
                    return $row->waktu_perawatan ? $row->waktu_perawatan . ' Bulan' : '-';
                })
                ->editColumn('waktu_perawatan', function ($row) {
                    return $row->waktu_perawatan ?? '-';
                })
                ->setRowClass(function ($row) {
                    $waktuPerawatan = 0;

                    if (!empty($row->waktu_perawatan) && is_numeric(trim($row->waktu_perawatan))) {
                        $waktuPerawatan = (int) trim($row->waktu_perawatan);
                    }

                    $jatuhTempo = $row->tanggal_perawatan
                        ? \Carbon\Carbon::parse($row->tanggal_perawatan)->addMonths($waktuPerawatan)
                        : null;

                    return ($jatuhTempo && $jatuhTempo->isPast()) ? 'table-danger' : '';
                })


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

                $waktu = (int) $assetData['waktu']; // konversi ke integer!

                if ($waktu > 0) {
                    $tanggal_perawatan = now()->addMonths($waktu);
                    $asset->tanggal_perawatan = $tanggal_perawatan;
                }

                $asset->save();
            }


            return response()->json([
                'success' => true,
                'message' => 'Tanggal perawatan berhasil diperbarui.'
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

        // Filter kecamatan jika bukan admin
        if ($user->role != 'admin') {
            $query->whereHas('sekolah', function ($q) use ($user) {
                $q->where('kecamatan_id', $user->kecamatan_id);
            });
        }

        // Filter waktu perawatan jika ada
        if ($request->filled('waktu')) {
            $months = (int) $request->waktu;
            $cutoffDate = now()->subMonths($months)->startOfDay();
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

        // dd($import->getErrors());

        if ($import->getErrors()) {
            return redirect()->route('asset.index')->with([
                'pesan' => 'Import data aset gagal. Silahkan periksa pesan error.',
                'list_errors' => $import->getErrors(),
                'level-alert' => 'alert-warning',
            ]);
        }

        return redirect()->route('asset.index')->with([
            'pesan' => 'Import data aset sepenuhnya telah berhasil!',
            'level-alert' => 'alert-success',
        ]);
    }
}
