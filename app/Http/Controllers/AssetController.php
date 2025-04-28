<?php

namespace App\Http\Controllers;

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

            return DataTables::of($assetsQuery)
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

        return view('asset.index', compact('tags', 'places', 'asset'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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

            // Simpan ke history
            History::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'change_type' => 'create',
                'old_values' => null,
                'new_values' => json_encode($asset->getAttributes()),
                'changed_fields' => json_encode(array_keys($validated)),
            ]);

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

            return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil diperbarui', 'level-alert' => 'alert-warning']);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'pesan' => 'Terjadi kesalahan saat memperbarui aset: ' . $e->getMessage(),
                'level-alert' => 'alert-danger',
            ]);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        //
    }

    public function maintenance(Request $request)
    {
        if ($request->ajax()) {
            $data = Asset::query()
                ->whereIn('kondisi', ['Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat'])
                ->select(['id', 'name', 'kondisi', 'tanggal_perawatan', 'harga_perawatan', 'waktu_perawatan']);

            // Filter berdasarkan waktu perawatan (dalam bulan)
            if ($request->filled('waktu')) {
                $months = (int) $request->waktu;
                $cutoff = Carbon::now()->subMonths($months);
                $data->whereDate('tanggal_perawatan', '>=', $cutoff);
            }

            return DataTables::of($data)->make(true);
        }

        return view('asset.maintenance');
    }

    /**
     * Date: 28-04-2025
     * Export List Asset to Excel
     */
    public function export() {
        $date = date('Y-m-d');
        $fileName = "Laporan Data Aset - $date.xlsx";

        return Excel::download(new AssetsExport, $fileName);
    }

    /**
     * Date: 28-04-2025
     * Import Data Asset from Excel
     *
     * ! Belum Selesai
     */
    public function import() {

    }
}
