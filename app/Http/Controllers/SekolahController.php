<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sekolahs = Sekolah::with('kecamatan')
                ->withCount('assets');

            // Role 1 (admin) dan Role 2 (dispora) dapat melihat semua data
            if (in_array(Auth::user()->role_id, [1, 2])) {
                $sekolahs = $sekolahs->get(); // Ambil semua sekolah
            }
            // Role 3 hanya dapat melihat data sekolah yang ada di kecamatan mereka
            else {
                $sekolahs = $sekolahs->where('kecamatan_id', Auth::user()->kecamatan_id)->get(); // Filter berdasarkan kecamatan
            }

            return DataTables::of($sekolahs)
                ->addColumn('kecamatan', function ($row) {
                    return $row->kecamatan ? $row->kecamatan->name : '-';
                })
                ->addColumn('assets_count', function ($row) {
                    return $row->assets_count;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="javascript:void(0)" class="btn btn-link p-0 px-2"
                        data-bs-toggle="modal" data-bs-target="#editSekolahModal"
                        onclick="editSekolah(' . $row->id . ', \'' . e($row->name) . '\', ' . $row->kecamatan_id . ', \'' . $row->category . '\')">
                        <i class="fa-solid fa-pencil" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"></i>
                    </a>
                    <a href="javascript:void(0)" class="btn btn-link text-danger p-0 px-2"
                        onclick="deleteSekolah(' . $row->id . ')">
                        <i class="fa-solid fa-trash" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"></i>
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
            'kecamatan_id' => $request->kecamatan_id,
        ]);

        return redirect()->route('sekolah.index')->with(['pesan' => 'Sekolah berhasil ditambahkan', 'level-alert' => 'alert-success']);
    }

    public function edit($id)
    {
        $sekolah = Sekolah::findOrFail($id);
        return response()->json($sekolah);
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
