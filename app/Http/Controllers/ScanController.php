<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScanExport;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('scan.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scan $scan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scan $scan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scan $scan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scan $scan)
    {
        //
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
        $scan->category = 'dokumen';
        $scan->save();

        // Update status asset berdasarkan RFID
        $this->updateRFIDStatus(Asset::class, $tags);

        // Buat log scan tanpa database
        $scanData = [
            'total' => count($tags),
            'category' => 'dokumen',
            'tags' => $tags
        ];

        // Simpan log scan ke cache (TTL 30 detik)
        Cache::put('latest_scan_asset', $scanData, 30);

        return response()->json([
            'status' => 'success',
            'message' => 'RFID scanned successfully',
            'total_scanned' => count($tags),
            'category' => 'dokumen'
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
