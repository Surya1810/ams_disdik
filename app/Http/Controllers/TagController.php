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
            $data = Tag::with('kecamatan')->select('rfid_number', 'status', 'kecamatan_id');
            return DataTables::of(Tag::with('kecamatan')) // Load relasi kecamatan
                ->addColumn('kecamatan', function ($tag) {
                    return $tag->kecamatan ? $tag->kecamatan->name : '-'; // Ambil nama kecamatan
                })
                ->addColumn('action', function ($tag) {
                    return '<button onclick="deleteTag(' . "'" . $tag->rfid_number . "'" . ')" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-trash"></i>
            </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $kecamatan = Kecamatan::all();
        return view('tag.index', compact('kecamatan'));
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
        $fileName = "exportTag-$date.xlsx";

        return Excel::download(new TagsExport, $fileName);
    }
}
