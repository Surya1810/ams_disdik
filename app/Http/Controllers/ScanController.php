<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScanExport;

use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        // Hitung status missing and found
        $statusMissingCount = $roleId == 1
            ? Asset::where('is_there', 0)->count()
            : Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })->where('is_there', 0)->get()->count();
        $statusFoundCount = $roleId == 1
            ? Asset::where('is_there', 1)->count()
            : Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })->where('is_there', 1)->get()->count();
        $status = [
            'foundCount' => $statusFoundCount,
            'missingCount' => $statusMissingCount
        ];

        // Untuk role 1, bisa melihat semua aset
        if ($roleId == 1) {
            $assets = Asset::all();
        } else {
            // Untuk role 2 dan 3, hanya dapat melihat aset di kecamatan dan sekolah mereka
            $assets = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
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
            if ($request->filled('is_there')) {
                $assetsQuery->where('is_there', $request->is_there);
            }

            return DataTables::of($assetsQuery)
                ->addColumn('is_there', fn($row) => $row->is_there ? '<strong>FOUND</strong>' : '<strong>MISSING</strong>')
                ->rawColumns(['is_there'])
                ->make(true);
        }

        // Hitung Scan
        if ($roleId == 1) {
            $scansCount = Scan::sum('total');
            $lastScan = Scan::orderBy('id', 'DESC')->first();
        } else {
            $scansCount = Scan::where('user_id', Auth::user()->id)->sum('total');
            $lastScan = Scan::orderBy('id', 'DESC')
                ->where('user_id', Auth::user()->id)
                ->first();
        }

        return view('scan.index', compact('status', 'scansCount', 'lastScan'));
    }

    public function scannedAssets(Request $request) {
        $roleId = Auth::user()->role_id;

        if ($request->ajax()) {
            if ($roleId == 1) {
                $scans = Scan::orderBy('id', 'DESC')->get();
            } else {
                $scans = Scan::where('user_id', Auth::user()->id)
                    ->orderBy('id', 'DESC')->get();
            }

            return DataTables::of($scans)
                ->addColumn('created_at', function ($scan) {
                    return $scan->created_at->format('Y-m-d');
                })->make(true);
        }
    }

    /**
     * Proses scan RFID untuk Dokumen.
     */
    public function scanAsset(Request $request)
    {
        // Ambil data RFID dari request (harus dalam bentuk array)
        $tags = $request->input('data');

        // Validasi: Pastikan data adalah array dan tidak kosong
        if (!is_array($tags) || empty($tags)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid RFID data received'
            ], 400);
        }

        // Simpan log scan ke database
        $scan = new Scan();
        $scan->total = count($tags);
        $scan->user_id = Auth::user()->id;
        $scan->save();

        // Update status asset berdasarkan RFID
        $this->updateRFIDStatus(Asset::class, $tags);

        return response()->json([
            'status' => 'success',
            'message' => 'RFID scanned successfully',
            'total_scanned' => count($tags),
        ]);
    }

    public function getLatestScansAsset(Request $request)
    {
        $scans = Cache::get('latest_scan_asset', [
            'total' => 0,
            'category' => 'dokumen',
            'tags' => []
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $scans
        ]);
    }

    /**
     * Memperbarui status `is_there` dalam model.
     */
    private function updateRFIDStatus($model, $tags)
    {
        // Reset semua status `is_there` ke false terlebih dahulu
        $model::query()->update(['is_there' => false]);

        // Hanya update data yang ada dalam $tags
        $model::whereIn('rfid_number', $tags)->update(['is_there' => true]);
    }

    public function exportFound()
    {
        return Excel::download(new ScanExport('found'), 'Berita_Acara_Penemuan.xlsx');
    }

    public function exportMissing()
    {
        return Excel::download(new ScanExport('missing'), 'Berita_Acara_Kehilangan.xlsx');
    }
}
