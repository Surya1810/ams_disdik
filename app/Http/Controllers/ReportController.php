<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

// Models
use App\Models\Asset;
use App\Models\Tag;
use App\Models\Sekolah;
use App\Models\Kecamatan;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cek kecamatan_id dari user yang login
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        // Base assets query untuk role 2 dan 3
        $assetsQuery = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
            $query->where('kecamatan_id', $kecamatanId);
        })->with('sekolah')->get();

        // Hitung asset
        $assetsCount = ($roleId === 1 || $roleId === 2) ? Asset::count() : $assetsQuery->count();
        $assetsNilai = ($roleId === 1 || $roleId === 2)
            ? Asset::sum('nilai_perolehan')
            : $assetsQuery->sum('nilai_perolehan');
        $assetsHilangCount = ($roleId === 1 || $roleId === 2)
            ? Asset::where('is_there', false)->count()
            : $assetsQuery->where('is_there', false)->count();

        // Hitung perawatan
        $kondisiTidakDirawat = ['Baik', 'Hilang'];
        $perawatanCount = ($roleId === 1 || $roleId === 2)
            ? Asset::whereNotIn('kondisi', $kondisiTidakDirawat)->count()
            : $assetsQuery->whereNotIn('kondisi', $kondisiTidakDirawat)->count();
        $perawatanNilai = ($roleId === 1 || $roleId === 2)
            ? Asset::whereNotIn('kondisi', $kondisiTidakDirawat)->sum('harga_perawatan')
            : $assetsQuery->whereNotIn('kondisi', $kondisiTidakDirawat)->sum('harga_perawatan');

        // Hitung kehilangan
        $kehilanganNilai = ($roleId === 1 || $roleId === 2)
            ? Asset::where('is_there', false)->sum('nilai_perolehan')
            : $assetsQuery->where('is_there', false)->sum('nilai_perolehan');

        // Hitung tag
        $tagsCount = ($roleId === 1 || $roleId === 2)
            ? Tag::count()
            : Tag::where('kecamatan_id', $kecamatanId)->count();
        $tagsUsedCount = ($roleId === 1 || $roleId === 2)
            ? Tag::where('status', 'used')->count()
            : Tag::where('kecamatan_id', $kecamatanId)->where('status', 'used')->count();

        // Tahun Pembelian
        $tahunPembelianArr = ($roleId === 1 || $roleId === 2)
            ? Asset::all()->pluck('tahun_pembelian')->unique()->values()->all()
            : $assetsQuery->pluck('tahun_pembelian')->unique()->values()->all();

        // Nama sekolah
        $sekolahOrKecamatanArr = ($roleId == 1 || $roleId == 2)
            ? Kecamatan::select('id', 'name')->whereNotIn('id', [1, 2])->get()
            : Sekolah::select('id', 'name', 'category')->where('kecamatan_id', $kecamatanId)->get();

        $data = [
            'assetsCount' => $assetsCount,
            'assetsNilai' => formatRupiah($assetsNilai),
            'perawatanCount' => $perawatanCount,
            'perawatanNilai' => formatRupiah($perawatanNilai),
            'kehilanganCount' => $assetsHilangCount,
            'kehilanganNilai' => formatRupiah($kehilanganNilai),
            'tagsCount' => $tagsCount,
            'tagsUsedCount' => $tagsUsedCount,
            'tahunPembelianArr' => $tahunPembelianArr,
            'sekolahOrKecamatanArr' => $sekolahOrKecamatanArr
        ];

        return view('report.index', compact('data'));
    }

    /**
     * Menampilkan nilai aset per tahun dengan ajax request
     */
    public function getNilaiPerTahunJSON(Request $request)
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        $query = Asset::groupBy('tahun_pembelian')
            ->select(
                'tahun_pembelian',
                DB::raw('COUNT(*) as total_asset'),
                DB::raw('SUM(nilai_perolehan) as total_nilai')
            )
            ->orderBy('tahun_pembelian', 'asc');

        if ($request->filled('tahun')) {
            $query = $query->where('tahun_pembelian', $request->tahun);
        }

        $assetPerTahun = ($roleId === 1 || $roleId === 2)
            ? $query->get()
            : $query->whereHas('sekolah', function ($q) use ($kecamatanId) {
                $q->where('kecamatan_id', $kecamatanId);
            })->get();

        // Hitung total nilai keseluruhan
        $grandTotalNilai = $assetPerTahun->sum('total_nilai');

        // Buat DataTables response secara manual
        $dataTableJson = DataTables::of($assetPerTahun)
            ->addColumn('total_nilai', function ($row) {
                return formatRupiah($row->total_nilai);
            })
            ->toArray();

        $dataTableJson['total_all_nilai'] = formatRupiah($grandTotalNilai);

        return response()->json($dataTableJson);
    }

    /**
     * Menampilkan nilai aset per sekolan dengan ajax request
     * Role == 3
     */
    public function getNilaiPerSekolahJSON(Request $request)
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;
        $query = Asset::join('sekolahs', 'assets.sekolah_id', '=', 'sekolahs.id')
            ->select(
                DB::raw("CONCAT(sekolahs.category, ' ', sekolahs.name) as nama_sekolah"),
                'assets.tahun_pembelian',
                DB::raw('COUNT(assets.id) as total_asset'),
                DB::raw('SUM(assets.nilai_perolehan) as total_nilai')
            )
            ->groupBy('nama_sekolah', 'assets.tahun_pembelian')
            ->orderBy('nama_sekolah')
            ->orderBy('assets.tahun_pembelian');

        if ($request->filled('sekolah')) {
            $query = $query->where('sekolah_id', $request->sekolah);
        }

        $assetPerSekolah = $roleId == 1
            ? $query->get()
            : $query->whereHas('sekolah', function ($q) use ($kecamatanId) {
                $q->where('kecamatan_id', $kecamatanId);
            })->get();

        // Hitung total nilai keseluruhan
        $grandTotalNilai = $assetPerSekolah->sum('total_nilai');

        // Buat DataTables response secara manual
        $dataTableJson = DataTables::of($assetPerSekolah)
            ->addColumn('total_nilai', function ($row) {
                return formatRupiah($row->total_nilai);
            })
            ->toArray();

        $dataTableJson['total_all_nilai'] = formatRupiah($grandTotalNilai);

        return response()->json($dataTableJson);
    }

    /**
     * Menampilkan nilai aset per kecamatan dengan ajas request
     *
     * Role == 2
     */
    public function getNilaiPerKecamatanJSON(Request $request)
    {
        if (Auth::user()->role_id != 2) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Access Denied'
            ], 403);
        }

        $query = Kecamatan::with([
                'sekolahs' => function ($query) {
                    $query->withSum('assets', 'nilai_perolehan')
                        ->withCount('assets');
                }
            ])
            ->select('id', 'name')
            ->whereNotIn('id', [1, 2]);

        if ($request->filled('kecamatan')) {
            $query = $query->where('id', $request->kecamatan);
        }

        $kecamatanData = $query->get();
        $grandTotal = 0;

        foreach ($kecamatanData as $kecamatan) {
            if ($kecamatan->sekolahs->isEmpty()) {
                $kecamatan->sekolahs_sum_nilai_perolehan = 0;
                $kecamatan->sekolahs_total_assets = 0;
            } else {
                $sum = $kecamatan->sekolahs->sum('assets_sum_nilai_perolehan');
                $kecamatan->sekolahs_sum_nilai_perolehan = $sum;
                $kecamatan->sekolahs_total_assets = $kecamatan->sekolahs->sum('assets_count');
                $grandTotal += $sum;
            }
        }

        $data = $kecamatanData->map(function ($kecamatan) {
            return [
                'kecamatan' => $kecamatan->name,
                'total_nilai_perolehan' => $kecamatan->sekolahs_sum_nilai_perolehan
                    ? formatRupiah($kecamatan->sekolahs_sum_nilai_perolehan)
                    : 0,
                'total_sekolah' => count($kecamatan->sekolahs),
                'total_assets' => $kecamatan->sekolahs_total_assets,
            ];
        });

        $dataTable = DataTables::of(collect($data))
            ->addColumn('total_nilai_perolehan', fn($row) => $row['total_nilai_perolehan'])
            ->addColumn('total_sekolah', fn($row) => $row['total_sekolah'])
            ->addColumn('total_assets', fn($row) => $row['total_assets'])
            ->toArray();
        $dataTable['total_all_nilai'] = formatRupiah($grandTotal);

        return response()->json($dataTable);
    }
}
