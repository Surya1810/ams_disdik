<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Kecamatan;
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
            $sekolahs = Sekolah::with('kecamatan')
                ->withCount('assets')
                ->whereHas('kecamatan', function ($query) {
                    $query->whereNotIn('id', [1, 2]); // Optional: filter kecamatan tertentu
                });

            return DataTables::of($sekolahs)
                ->addColumn('kecamatan', function ($row) {
                    return $row->kecamatan ? $row->kecamatan->name : '-';
                })
                ->addColumn('assets_count', function ($row) {
                    return $row->assets_count;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a role="button" class="text-warning px-3 mb-0 border-radius-lg" 
                        data-bs-toggle="modal" data-bs-target="#editSekolahModal" 
                        onclick="editSekolah(' . $row->id . ', \'' . $row->name . '\')">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                    <a role="button" class="text-danger px-3 mb-0 border-radius-lg" 
                        onclick="deleteSekolah(' . $row->id . ')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <form id="delete-form-' . $row->id . '" action="' . route('sekolah.destroy', $row->id) . '" method="POST" style="display: none;">
                        ' . csrf_field() . method_field('DELETE') . '
                    </form>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $kecamatans = Kecamatan::all();
        return view('sekolah.index', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'kecamatan_id' => 'required|integer|exists:kecamatans,id',
        ]);

        Sekolah::create([
            'name' => $request->name,
            'category' => $request->category,
            'kecamatan_id' => $request->kecamatan_id, // Pastikan kecamatan_id disertakan
        ]);

        return redirect()->route('sekolah.index');
    }

    public function edit($id)
    {
        $sekolah = Sekolah::findOrFail($id); // Ambil data sekolah berdasarkan ID
        return response()->json($sekolah); // Kembalikan data dalam format JSON
    }

    public function create()
    {
        $kecamatans = Kecamatan::all();
        return view('sekolah.create', compact('kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sekolah $sekolah)
    {
        $request->validate([
            'name' => 'required|string|unique:sekolahs,name,' . $sekolah->id,
            'kecamatan_id' => 'required|exists:kecamatans,id',
        ]);

        $sekolah->update([
            'name' => $request->input('name'),
            'kecamatan_id' => $request->input('kecamatan_id'),
        ]);

        return redirect()->route('sekolah.index')->with([
            'pesan' => 'Sekolah berhasil diperbarui',
            'level-alert' => 'alert-warning'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sekolah $sekolah)
    {
        if ($sekolah->assets()->count() > 0) {
            return redirect()->route('sekolah.index')->with([
                'pesan' => 'Tidak bisa menghapus, sekolah masih memiliki aset.',
                'level-alert' => 'alert-danger'
            ]);
        }

        $sekolah->delete();

        return redirect()->route('sekolah.index')->with([
            'pesan' => 'Sekolah berhasil dihapus',
            'level-alert' => 'alert-success'
        ]);
    }
}
