<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Asset;
use App\Models\Tag;

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
        $assetsCount = $roleId == 1 ? Asset::count() : $assetsQuery->count();
        $assetsHilangCount = $roleId == 1
            ? Asset::where('kondisi', 'Hilang')->count()
            : $assetsQuery->where('kondisi', 'Hilang')->count();

        // Hitung perawatan
        $kondisiTidakDirawat = ['Baik', 'Hilang'];
        $perawatanCount = $roleId == 1
            ? Asset::whereNotIn('kondisi', $kondisiTidakDirawat)->count()
            : $assetsQuery->whereNotIn('kondisi', $kondisiTidakDirawat)->count();
        $perawatanNilai = $roleId == 1
            ? Asset::whereNotIn('kondisi', $kondisiTidakDirawat)->sum('harga_perawatan')
            : $assetsQuery->whereNotIn('kondisi', $kondisiTidakDirawat)->sum('harga_perawatan');

        // Hitung kehilangan
        $kehilanganNilai = $roleId == 1
            ? Asset::where('kondisi', 'Hilang')->sum('nilai_perolehan')
            : $assetsQuery->where('kondisi', 'Hilang')->sum('nilai_perolehan');

        // Hitung tag
        $tagsCount = $roleId === 1
            ? Tag::count()
            : Tag::where('kecamatan_id', $kecamatanId)->count();

        $data = [
            'assetsCount' => $assetsCount,
            'perawatanCount' => $perawatanCount,
            'perawatanNilai' => formatRupiah($perawatanNilai),
            'kehilanganCount' => $assetsHilangCount,
            'kehilanganNilai' => formatRupiah($kehilanganNilai),
            'tagsCount' => $tagsCount
        ];

        // dd($data);

        return view('report.index', compact('data'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
