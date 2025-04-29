<?php

namespace App\Http\Controllers;

use App\Exports\TagsExport;
use App\Models\Tag;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Tag::with('kecamatan')) // Load relasi kecamatan
                ->addColumn('kecamatan', function ($tag) {
                    return $tag->kecamatan ? $tag->kecamatan->name : '-';
                })
                ->addColumn('status', function ($tag) {
                    if (strtolower($tag->status) == 'available') {
                        return '<span class="badge bg-success text-white">Available</span>';
                    } elseif (strtolower($tag->status) == 'used') {
                        return '<span class="badge bg-danger text-white">Used</span>';
                    } else {
                        return '<span class="badge bg-secondary text-white">' . ucfirst($tag->status) . '</span>';
                    }
                })
                ->addColumn('action', function ($tag) {
                    if (strtolower($tag->status) !== 'available') {
                        return ''; // Tidak ada aksi jika status bukan available
                    }

                    return '
                    <a role="button" class="text-danger px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                        onclick="deleteTag(\'' . $tag->rfid_number . '\')">
                    <i class="fa-solid fa-trash"></i>
                    </a>
                    ';
                })


                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $kecamatan = Kecamatan::all();
        $availableTags = Tag::where('status', 'available')->orderBy('rfid_number')->get();
        return view('tag.index', compact('kecamatan', 'availableTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required|numeric',
            'until' => 'required|numeric',
            'kecamatan_id' => 'required|exists:kecamatans,id'
        ]);

        // Cek RFID yang sudah ada
        $existingTags = Tag::whereIn('rfid_number', range($request->from, $request->until))
            ->pluck('rfid_number')->toArray();
        if (count($existingTags) > 0) {
            return response()->json([
                'message' => 'RFID sudah ada: ' . implode(', ', $existingTags)
            ], 422);
        }

        // Inject RFID
        $tags = [];
        for ($i = (int) $request->from; $i <= (int) $request->until; $i++) {
            $rfid = str_pad($i, strlen($request->until), '0', STR_PAD_LEFT);
            $tags[] = [
                'rfid_number' => $rfid,
                'status' => 'available',
                'kecamatan_id' => $request->kecamatan_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Tag::insert($tags);
        return response()->json(['message' => 'RFID berhasil diinject!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($rfid_number)
    {
        $tag = Tag::where('rfid_number', $rfid_number)->first();

        if (!$tag) {
            return redirect()->back()->with(['pesan' => 'Tag not found', 'level-alert' => 'alert-danger']);
        }

        if ($tag->document == null) {
            $tag->delete();
            return redirect()->back()->with(['pesan' => 'Tag deleted successfully', 'level-alert' => 'alert-success']);
        } else {
            return redirect()->back()->with(['pesan' => 'Tag is used in a document', 'level-alert' => 'alert-danger']);
        }
    }

    public function export()
    {
        $date = date('Y-m-d');
        $fileName = "List Tag RFID - $date.xlsx";

        return Excel::download(new TagsExport, $fileName);
    }

    public function distribute(Request $request)
    {
        $from = $request->from;
        $until = $request->until;
        $kecamatanId = $request->kecamatan_id;

        // Ambil semua tag dalam range
        $tags = Tag::whereBetween('rfid_number', [$from, $until])->get();

        $availableTags = $tags->where('status', 'available');
        $totalAvailable = $availableTags->count();

        if ($totalAvailable === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Distribusi gagal: Terdapat RFID yang sudah digunakan dalam rentang tersebut.'
            ], 422);
        }

        // Update hanya yang available
        Tag::whereBetween('rfid_number', [$from, $until])
            ->where('status', 'available')
            ->update(['kecamatan_id' => $kecamatanId]);

        return response()->json([
            'status' => 'success',
            'message' => "Sebanyak {$totalAvailable} tag berhasil didistribusikan."
        ]);
    }
}
