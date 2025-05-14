<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Scan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ScannedTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        $scansCount = Auth::user()->role_id == 2
            ? Scan::whereNot('user_id', 1)->sum('total')
            : Scan::where('user_id', Auth::user()->id)->sum('total');
        $lastScan = Auth::user()->role_id == 2
            ? Scan::whereNot('user_id', 1)->latest()->first()
            : Scan::where('user_id', Auth::user()->id)
            ->latest()
            ->first();

        return view('scan.index', compact('scansCount', 'lastScan'));
    }

    public function scannedAssets(Request $request)
    {
        if ($request->ajax()) {
            $query = Auth::user()->role_id == 2
                ? Scan::whereNot('user_id', 1)->latest()
                : Scan::where('user_id', Auth::user()->id)->latest();

            return DataTables::of($query)
                ->addColumn('created_at', function ($scan) {
                    return $scan->created_at->format('Y-m-d H:i');
                })
                ->addColumn('actions', function ($row) {
                    $button = '<a href="' . route('scanned.detail', $row->id) . '" class="btn btn-link p-0 edit-asset"><i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"></i></a>';

                    return $button;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function scannedDetail(Request $request, Scan $scan)
    {
        $user = Auth::user();

        // Validasi akses user
        if ($user->role_id !== 2 && $scan->user_id !== $user->id) {
            abort(404, 'History Stock Opname Tidak Ditemukan');
        }

        $query = ScannedTag::where('scan_id', $scan->id);

        // Jika request ajax (DataTables)
        if ($request->ajax()) {
            if ($request->filled('is_there')) {
                $query->where('is_there', $request->is_there);
            }

            return DataTables::of($query)
                ->addColumn('is_there', fn($row) => $row->is_there ? '<strong>FOUND</strong>' : '<strong>MISSING</strong>')
                ->addColumn('actions', function ($row) {
                    $assetExists = Asset::where('rfid_number', $row->rfid_number)->exists();

                    if ($assetExists) {
                        return '<button type="button" class="badge bg-primary border-0 edit-asset" data-rfid="' . $row->rfid_number . '" onclick="buttonModalShowAset(this)"><i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Detail"></i></button>';
                    }
                    return '<small class="badge bg-danger">Aset Sudah Tidak Terdaftar</small>';
                })
                ->rawColumns(['is_there', 'actions'])
                ->make(true);
        }

        // Hitung status ditemukan dan hilang
        $statusCounts = [
            'foundCount' => (clone $query)->where('is_there', true)->count(),
            'missingCount' => (clone $query)->where('is_there', false)->count(),
        ];

        return view('scan.detail', compact('scan', 'statusCounts'));
    }

    public function scannedAssetDetail(Request $request, $rfid)
    {
        if ($request->ajax()) {
            $asset = Asset::where('rfid_number', $rfid)
                ->with('sekolah')
                ->first();

            if ($asset->exists()) {
                $asset->foto_awal = (!$asset->foto_awal || $asset->foto_awal === 'dummy.jpg')
                    ? asset('assets/Image/no_image.png')
                    : asset(Storage::url('/public/assets/' . $asset->foto_awal));
                $asset->nilai_perolehan = formatRupiah($asset->nilai_perolehan);
                $asset->harga_perawatan = formatRupiah($asset->harga_perawatan);

                return response()->json([
                    'asset' => $asset
                ]);
            }

            return response()->json([
                'status' => 'fail',
                'message' => 'Asset tidak ditemukan'
            ], 404);
        }

        abort(403);
    }

    public function exportFound($id)
    {
        $assets = ScannedTag::where('scan_id', $id)
            ->where('is_there', true)
            ->get();
        $scan = Scan::find($id);

        $pdf = PDF::loadView('scan.scan_pdf', [
            'assets' => $assets,
            'scan'   => $scan,
            'status' => 'found'
        ]);

        return $pdf->download('berita_acara_penemuan.pdf');
    }


    public function exportMissing($id)
    {
        $assets = ScannedTag::where('scan_id', $id)
            ->where('is_there', false)
            ->get();
        $scan = Scan::find($id);

        $pdf = PDF::loadView('scan.scan_pdf', [
            'assets' => $assets,
            'scan'   => $scan,
            'status' => 'missing'
        ]);

        return $pdf->download('berita_acara_kehilangan.pdf');
    }
}
