<?php

namespace App\Http\Controllers;

use App\Exports\TagsExport;
use App\Models\Tag;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roleId = Auth::user()->role_id;
        $kecamatanId = Auth::user()->kecamatan_id;

        if ($request->ajax()) {
            $tags = ($roleId == 1 || $roleId == 2)
                ? Tag::with('kecamatan')
                : Tag::with('kecamatan')->where('kecamatan_id', $kecamatanId);

            if ($request->filled('status')) {
                $tags = $tags->where('status', $request->status);
            }

            $dataTables = DataTables::of($tags)
                ->filter(function ($query) use ($request) {
                    if ($request->filled('search.value')) {
                        $search = $request->input('search.value');
                        $query->where(function ($q) use ($search) {
                            $q->where('rfid_number', 'like', "%{$search}%")
                                ->orWhere('status', 'like', "%{$search}%")
                                ->orWhereHas('kecamatan', function ($q2) use ($search) {
                                    $q2->where('name', 'like', "%{$search}%");
                                });
                        });
                    }
                })
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
                });

            // Tambahkan kolom action hanya jika role 1 atau 2
            if (in_array($roleId, [1, 2])) {
                $dataTables = $dataTables->addColumn('action', function ($tag) {
                    if (strtolower($tag->status) !== 'available') {
                        return '';
                    }
                    return '
                    <a role="button" class="text-danger px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                        onclick="deleteTag(\'' . $tag->rfid_number . '\')">
                    <i class="fa-solid fa-trash"></i>
                    </a>
                ';
                })
                    ->rawColumns(['status', 'action']);
            } else {
                // Jika role bukan 1 atau 2, hanya rawColumns status saja
                $dataTables = $dataTables->rawColumns(['status']);
            }

            return $dataTables->make(true);
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

    public function export(Request $request)
    {
        $date = date('Y-m-d');
        $fileName = "List Tag RFID - $date";
        $status = $request->query('status');

        return Excel::download(
            new TagsExport($status),
            $fileName . ' - ' . ($status ? strtoupper($status) : 'SEMUA STATUS') . '.xlsx'
        );
    }

    public function distribute(Request $request)
    {
        $from = $request->from;
        $until = $request->until;
        $kecamatanId = $request->kecamatan_id;

        // Validasi tag Dispora yang tersedia
        $availableDisporaTags = Tag::whereBetween('rfid_number', [$from, $until])
            ->where('status', 'available')
            ->where('kecamatan_id', 2)
            ->count();

        if ($availableDisporaTags === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada tag Dispora yang tersedia dalam rentang ini'
            ], 422);
        }

        // Update distribusi
        Tag::whereBetween('rfid_number', [$from, $until])
            ->where('status', 'available')
            ->where('kecamatan_id', 2)
            ->update(['kecamatan_id' => $kecamatanId]);

        return response()->json([
            'status' => 'success',
            'message' => "Sebanyak {$availableDisporaTags} tag berhasil didistribusikan."
        ]);
    }
}
