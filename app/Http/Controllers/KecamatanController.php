<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Tag;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ambil data kecamatan dengan jumlah sekolah, kecuali id 1 dan 2
            $kecamatans = Kecamatan::withCount('sekolahs')
                ->whereNotIn('id', [1, 2]);

            return DataTables::of($kecamatans)
                ->filter(function ($query) use ($request) {
                    if (!empty($request->search['value'])) {
                        $search = $request->search['value'];
                        $query->where('name', 'like', "%{$search}%");
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button type="button" class="btn btn-danger mb-0 rounded-partner"
                        onclick="deleteKecamatan(' . $row->id . ')"><i class="fa-solid fa-trash"></i></button>
                    <form id="delete-form-' . $row->id . '" 
                        action="' . route('kecamatan.destroy', $row->id) . '" 
                        method="POST" style="display: none;">
                        ' . csrf_field() . method_field('DELETE') . '
                    </form>
                ';
                })
                ->rawColumns(['action']) // Izinkan HTML dalam kolom action
                ->make(true);
        }

        return view('kecamatan.index');
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
        ]);

        $old = session()->getOldInput();

        Kecamatan::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('kecamatan.index')->with(['pesan' => 'Kecamatan berhasil ditambahkan', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kecamatan $kecamatan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kecamatan $kecamatan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kecamatan $kecamatan)
    {
        // Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);

        $kecamatan->update($validatedData);

        return redirect()->route('kecamatan.index')->with(['pesan' => 'Kecamatan berhasil diperbarui', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kecamatan $kecamatan)
    {
        //
    }
}
