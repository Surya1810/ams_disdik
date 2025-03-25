<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Tag;
use Illuminate\Http\Request;

class SekolahController extends Controller
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
            'name' => 'required|string',
            'kecamatan_id' => 'required',
        ]);

        $kecamatan = Sekolah::create([
            'name' => $request->input('name'),
            'kecamatan_id' => $request->input('kecamatan_id'),
        ]);

        return redirect()->route('sekolah.index')->with(['pesan' => 'Sekolah berhasil ditambahkan', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sekolah $sekolah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sekolah $sekolah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sekolah $sekolah)
    {
        // Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => 'required|string',
            'kecamatan_id' => 'required|string',
        ]);

        $sekolah->update($validatedData);

        return redirect()->route('sekolah.index')->with(['pesan' => 'Sekolah berhasil diperbarui', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sekolah $sekolah)
    {
        //
    }
}
