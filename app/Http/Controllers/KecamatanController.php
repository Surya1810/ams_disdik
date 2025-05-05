<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
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
                <a href="javascript:void(0)" class="text-primary px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"
                    onclick="editKecamatan(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                    <i class="fa-solid fa-pencil"></i>
                </a>
                <a href="javascript:void(0)" class="text-danger px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                    onclick="deleteKecamatan(' . $row->id . ')">
                    <i class="fa-solid fa-trash"></i>
                </a>
                <form id="delete-form-' . $row->id . '" action="' . route('kecamatan.destroy', $row->id) . '" method="POST" style="display: none;">
                    ' . csrf_field() . method_field('DELETE') . '
                </form>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('kecamatan.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        Kecamatan::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('kecamatan.index')->with(['pesan' => 'Kecamatan berhasil ditambahkan', 'level-alert' => 'alert-success']);
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

        return redirect()->route('kecamatan.index')->with(['pesan' => 'Kecamatan berhasil diperbarui', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kecamatan $kecamatan)
    {
        if ($kecamatan->sekolahs()->exists()) {
            return redirect()->back()->with(['pesan' => 'Kecamatan ini memiliki sekolah, tidak bisa dihapus.', 'level-alert' => 'alert-danger']);
        }

        $kecamatan->delete();
        return redirect()->route('kecamatan.index')->with(['pesan' => 'Kecamatan berhasil dihapus', 'level-alert' => 'alert-success']);
    }
}
