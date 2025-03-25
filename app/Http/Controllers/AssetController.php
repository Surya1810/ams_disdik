<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'tag' => 'required|exists:tags,rfid_number',
            'sekolah_id' => 'required',

            'kode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'register' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'bahan' => 'required|string|max:255',
            'tahun_pembelian' => 'required|year',
            'pabrik' => 'required|string|max:255',

            'rangka' => 'string|max:255',
            'mesin' => 'string|max:255',
            'polisi' => 'string|max:255',
            'bpkb' => 'string|max:255',

            'nip_pic' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'jabatan_pic' => 'required|string|max:255',
            'telp_pic' => 'required|min:10',

            'asal_perolehan' => 'string|max:255',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required',
            'tanggal_perawatan' => 'required|date',
            'harga_perawatan' => 'required|numeric|min:0',

            'gedung' => 'required',
            'lantai' => 'required',
            'ruangan' => 'required',
            'detail' => 'required',

            // validasi gambar dan fungsi gambar belum
        ]);

        $old = session()->getOldInput();

        Asset::create([
            'tag' => $request->input('tag'),
            'sekolah_id' => $request->input('sekolah_id'),
            'kode' => $request->input('kode'),
            'name' => $request->input('name'),
            'register' => $request->input('register'),
            'merk' => $request->input('merk'),
            'ukuran' => $request->input('ukuran'),
            'bahan' => $request->input('bahan'),
            'tahun_pembelian' => $request->input('tahun_pembelian'),
            'pabrik' => $request->input('pabrik'),
            'rangka' => $request->input('rangka'),
            'mesin' => $request->input('mesin'),
            'polisi' => $request->input('polisi'),
            'bpkb' => $request->input('bpkb'),
            'nip_pic' => $request->input('nip_pic'),
            'nama_pic' => $request->input('nama_pic'),
            'jabatan_pic' => $request->input('jabatan_pic'),
            'telp_pic' => $request->input('telp_pic'),
            'asal_perolehan' => $request->input('asal_perolehan'),
            'nilai_perolehan' => $request->input('nilai_perolehan'),
            'kondisi' => $request->input('kondisi'),
            'tanggal_perawatan' => $request->input('tanggal_perawatan'),
            'harga_perawatan' => $request->input('harga_perawatan'),
            'gedung' => $request->input('gedung'),
            'lantai' => $request->input('lantai'),
            'ruangan' => $request->input('ruangan'),
            'detail' => $request->input('detail'),
        ]);

        return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil ditambahkan', 'level-alert' => 'alert-success']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil diperbarui', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        // 
    }
}
