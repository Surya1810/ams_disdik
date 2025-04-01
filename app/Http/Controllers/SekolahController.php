<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Tag;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data sekolah dengan jumlah sekolah, kecuali id 1 dan 2
            $sekolahs = Sekolah::withCount('assets')->with('kecamatan')
                ->whereNotIn('id', [1, 2]);

            return DataTables::of($sekolahs)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where('name', 'like', "%{$search}%");
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a role="button" class="text-danger px-3 mb-0 border-radius-lg"
                        onclick="deleteSekolah(' . $row->id . ')"><i class="fa-solid fa-trash"></i></a>
                    <form id="delete-form-' . $row->id . '" 
                        action="' . route('sekolah.destroy', $row->id) . '" 
                        method="POST" style="display: none;">
                        ' . csrf_field() . method_field('DELETE') . '
                    </form>
                ';
                })
                ->rawColumns(['action']) // Izinkan HTML dalam kolom action
                ->make(true);
        }

        return view('sekolah.index');
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
