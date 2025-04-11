<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Asset;
use App\Models\History;
use App\Models\Sekolah;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Approval::with(['asset', 'requester'])->latest();

            if ($request->has('status') && $request->status !== null) {
                $data->where('status', $request->status);
            }

            if ($request->has('type') && $request->type !== null) {
                $data->where('type', $request->type);
            }

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name)
                ->addColumn('requester', fn($row) => $row->requester->name)
                ->addColumn('keterangan', function ($row) {
                    return $row->payload['keterangan'] ?? '-';
                })
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['asset_name', 'requested_by', 'from', 'to', 'status'])
                ->addColumn('rejection_note', fn($row) => $row->rejection_note)
                ->make(true);
        }

        return view('approval.index');
    }

    public function mutation(Request $request)
    {
        if ($request->ajax()) {
            $approvals = Approval::with(['asset', 'requester'])
                ->where('type', 'mutation')
                ->select('approvals.*')
                ->where('requested_by', Auth::user()->id)
                ->latest();

            return DataTables::of($approvals)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['old'] ?? '-';
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['new'] ?? '-';
                })
                ->addColumn('requested_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['asset_name', 'requested_by', 'from', 'to', 'status'])
                ->make(true);
        }

        $assets = Asset::all();

        return view('asset.mutation', compact('assets'));
    }
    public function loan(Request $request)
    {
        if ($request->ajax()) {
            $approvals = Approval::with(['asset', 'requester'])
                ->where('type', 'loan')
                ->select('approvals.*')
                ->where('requested_by', Auth::user()->id)
                ->latest();

            return DataTables::of($approvals)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['old'] ?? '-';
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['new'] ?? '-';
                })
                ->addColumn('requested_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['asset_name', 'requested_by', 'from', 'to', 'status'])
                ->make(true);
        }

        $assets = Asset::all();
        $schools = Sekolah::all();

        return view('asset.loan', compact('assets', 'schools'));
    }
    public function disposal(Request $request)
    {
        if ($request->ajax()) {
            $data = Approval::with(['asset', 'requester'])
                ->where('type', 'disposal')
                ->whereHas('asset')
                ->select('approvals.*')
                ->where('requested_by', Auth::user()->id)
                ->latest();

            return DataTables::of($data)
                ->addColumn('asset_name', function ($row) {
                    return $row->asset->name ?? '-';
                })
                ->addColumn('requested_by', function ($row) {
                    return $row->requester->name ?? '-';
                })
                ->addColumn('jenis', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $jenis = $payload['jenis'] ?? '-';
                    $badge = $jenis === 'lelang' ? 'warning' : ($jenis === 'hilang' ? 'danger' : 'secondary');
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($jenis) . '</span>';
                })
                ->addColumn('keterangan', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['keterangan'] ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['jenis', 'status']) // biar badge HTML muncul
                ->make(true);
        }

        $assets = Asset::all();

        return view('asset.disposal', compact('assets'));
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
            'type' => 'required|in:mutation,loan,disposal',
            'asset_id' => 'required|exists:assets,id',
        ]);

        $type = $request->type;
        $payload = [];

        if ($type === 'mutation') {
            $payload = [
                'old' => [
                    'nip' => $request->old_nip,
                    'nama' => $request->old_nama,
                    'jabatan' => $request->old_jabatan,
                    'telp' => $request->old_telp,
                ],
                'new' => [
                    'nip' => $request->new_nip,
                    'nama' => $request->new_nama,
                    'jabatan' => $request->new_jabatan,
                    'telp' => $request->new_telp,
                ],
                'detail' => $request->detail,
            ];
        }

        if ($type === 'loan') {
            $payload = [
                'old' => [
                    'sekolah' => $request->old_sekolah,
                    'gedung' => $request->old_gedung,
                    'lantai' => $request->old_lantai,
                    'ruangan' => $request->old_ruangan,
                    'detail' => $request->old_detail,
                ],
                'new' => [
                    'sekolah_id' => $request->sekolah_id,
                    'gedung' => $request->gedung,
                    'lantai' => $request->lantai,
                    'ruangan' => $request->ruangan,
                    'detail' => $request->detail,
                ],
                'keterangan' => $request->keterangan,
            ];
        }

        if ($type === 'disposal') {
            $payload = [
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
            ];
        }

        // Simpan ke database
        Approval::create([
            'asset_id' => $request->asset_id,
            'type' => $type,
            'payload' => json_encode($payload),
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);
        if ($type === 'disposal') {
            return redirect()->route('asset.disposal')->with(['pesan' => 'Pengajuan disposal berhasil', 'level-alert' => 'alert-success']);
        } elseif ($type === 'loan') {
            return redirect()->route('asset.loan')->with(['pesan' => 'Pengajuan peminjaman berhasil', 'level-alert' => 'alert-success']);
        } elseif ($type === 'mutation')
            return redirect()->route('asset.mutation')->with(['pesan' => 'Pengajuan mutasi berhasil', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Approval $approval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Approval $approval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Approval $approval)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Approval $approval)
    {
        //
    }

    public function approve(Request $request)
    {
        // dd($request);

        // $request->validate([
        //     'ids' => 'required|array',
        //     'ids.*' => 'exists:approvals,id',
        // ]);

        // foreach ($request->ids as $id) {
        //     $approval = Approval::with('asset')->find($id);
        //     if (!$approval || $approval->status !== 'pending') continue;

        //     $payload = $approval->payload;
        //     $userId = Auth::user()->id;
        //     $asset = $approval->asset;

        //     switch ($approval->type) {
        //         case 'mutation':
        //             $fields = ['nip_pic', 'nama_pic', 'jabatan_pic', 'telp_pic'];
        //             $oldValues = $asset->only($fields);
        //             $newValues = array_intersect_key($payload, array_flip($fields));

        //             $asset->update($newValues);

        //             History::create([
        //                 'asset_id' => $asset->id,
        //                 'user_id' => $userId,
        //                 'change_type' => 'mutation',
        //                 'changed_fields' => json_encode(array_keys($newValues)),
        //                 'old_values' => json_encode($oldValues),
        //                 'new_values' => json_encode($newValues),
        //             ]);
        //             break;

        //         case 'loan':
        //             $fields = ['sekolah_id', 'gedung', 'lantai', 'ruangan', 'detail'];
        //             $oldValues = $asset->only($fields);
        //             $newValues = array_intersect_key($payload, array_flip($fields));

        //             $asset->update($newValues);

        //             History::create([
        //                 'asset_id' => $asset->id,
        //                 'user_id' => $userId,
        //                 'change_type' => 'location',
        //                 'changed_fields' => json_encode(array_keys($newValues)),
        //                 'old_values' => json_encode($oldValues),
        //                 'new_values' => json_encode($newValues),
        //             ]);
        //             break;

        //         case 'disposal':
        //             $deskripsi = $payload['deskripsi'] ?? '-';
        //             $jenis = $payload['jenis'] ?? '-';

        //             // 1. Buat History
        //             History::create([
        //                 'asset_id' => $asset->id,
        //                 'user_id' => $userId,
        //                 'change_type' => 'disposal',
        //                 'changed_fields' => json_encode(['jenis', 'deskripsi']),
        //                 'old_values' => null,
        //                 'new_values' => json_encode([
        //                     'jenis' => $jenis,
        //                     'deskripsi' => $deskripsi,
        //                 ]),
        //             ]);

        //             // 2. Update Tag ke 'available'
        //             if ($asset->tag) {
        //                 $tag = Tag::where('rfid_number', $asset->tag)->first();
        //                 if ($tag) {
        //                     $tag->status = 'available';
        //                     $tag->save();
        //                 }
        //             }

        //             // 3. Hapus gambar jika ada
        //             if ($asset->foto_awal) {
        //                 Storage::disk('public')->delete('assets/' . $asset->foto_awal);
        //                 $asset->foto_awal = null;
        //             }

        //             if ($asset->foto_kondisi) {
        //                 Storage::disk('public')->delete('assets/' . $asset->foto_kondisi);
        //                 $asset->foto_kondisi = null;
        //             }

        //             $asset->save();
        //             break;
        //     }

        //     $approval->update(['status' => 'approved']);
        // }

        return response()->json(['message' => 'Semua permintaan berhasil disetujui dan dicatat dalam history.']);
    }

    public function reject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'rejection_note' => 'required|string'
        ]);

        Approval::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejection_note' => $request->rejection_note
            ]);

        return response()->json(['message' => 'Semua permintaan telah ditolak.']);
    }
}
