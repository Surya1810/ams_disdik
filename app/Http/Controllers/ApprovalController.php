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
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Approval::with(['asset', 'requester'])->orderBy('approvals.created_at', 'desc');

            if ($request->filled('status')) {
                $data->where('status', $request->status);
            }

            if ($request->filled('type')) {
                $data->where('type', $request->type);
            }

            return DataTables::of($data)
                ->addColumn('checkbox', function ($row) {
                    if ($row->status === 'pending') {
                        return '<input type="checkbox" name="ids[]" class="row-checkbox" value="' . $row->id . '">';
                    }
                    return '';
                })
                ->addColumn('asset', fn($row) => $row->asset->name ?? '-')
                ->filterColumn('asset', function ($query, $keyword) {
                    $query->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
                })
                ->orderColumn('asset', function ($query, $order) {
                    $query->join('assets', 'approvals.asset_id', '=', 'assets.id')
                        ->orderBy('assets.name', $order)
                        ->select('approvals.*');
                })
                ->addColumn('requester', fn($row) => $row->requester->name ?? '-')
                ->filterColumn('requester', function ($query, $keyword) {
                    $query->whereHas('requester', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
                })
                ->orderColumn('requester', function ($query, $order) {
                    $query->join('users as u', 'approvals.requester_id', '=', 'u.id')
                        ->orderBy('u.name', $order)
                        ->select('approvals.*');
                })
                ->addColumn('keterangan', function ($row) {
                    // Pastikan payload didecode dengan benar
                    $payload = json_decode($row->payload, true); // Dekode string JSON ke array
                    return $payload['keterangan'] ?? '-'; // Ambil 'keterangan' atau tampilkan '-'
                })
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('rejection_note', fn($row) => $row->rejection_note ?? '-')
                ->rawColumns(['checkbox', 'status']) // Hanya kolom yang mengandung HTML
                ->make(true);
        }

        return view('approval.index');
    }

    public function mutation(Request $request)
    {
        if ($request->ajax()) {
            $approvals = Approval::with(['asset', 'requester'])
                ->where('type', 'mutation')
                ->where('requested_by', Auth::id())
                ->latest();

            return DataTables::of($approvals)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $from = $payload['from'] ?? null;
                    if ($from) {
                        return [
                            'nip_pic' => $from['nip_pic'] ?? '-',
                            'nama_pic' => $from['nama_pic'] ?? '-',
                            'jabatan_pic' => $from['jabatan_pic'] ?? '-',
                            'telp_pic' => $from['telp_pic'] ?? '-',
                        ];
                    }
                    return null;
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $to = $payload['to'] ?? null;
                    if ($to) {
                        return [
                            'nip_pic' => $to['nip_pic'] ?? '-',
                            'nama_pic' => $to['nama_pic'] ?? '-',
                            'jabatan_pic' => $to['jabatan_pic'] ?? '-',
                            'telp_pic' => $to['telp_pic'] ?? '-',
                        ];
                    }
                    return null;
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
                ->rawColumns(['status']) // 'from' dan 'to' bukan HTML di server, jadi tidak perlu rawColumns di sini
                ->make(true);
        }

        $assets = Asset::all();
        return view('asset.mutation', compact('assets'));
    }


    public function loan(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $approvals = Approval::with(['asset', 'requester'])
                ->where('type', 'loan')
                ->where('requested_by', Auth::id());

            return DataTables::of($approvals)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $old = $payload['old_values'] ?? [];

                    $sekolah = \App\Models\Sekolah::find($old['sekolah_id'] ?? null);
                    $sekolahName = $sekolah->name ?? '-';

                    return '<div>' .
                        '<strong>Sekolah:</strong> ' . $sekolahName . '<br>' .
                        '<strong>Gedung:</strong> ' . ($old['gedung'] ?? '-') . '<br>' .
                        '<strong>Lantai:</strong> ' . ($old['lantai'] ?? '-') . '<br>' .
                        '<strong>Ruangan:</strong> ' . ($old['ruangan'] ?? '-') . '<br>' .
                        '<strong>Detail:</strong> ' . ($old['detail'] ?? '-') .
                        '</div>';
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $new = $payload['new_values'] ?? [];

                    $sekolah = \App\Models\Sekolah::find($new['sekolah_id'] ?? null);
                    $sekolahName = $sekolah->name ?? '-';

                    return '<div>' .
                        '<strong>Sekolah:</strong> ' . $sekolahName . '<br>' .
                        '<strong>Gedung:</strong> ' . ($new['gedung'] ?? '-') . '<br>' .
                        '<strong>Lantai:</strong> ' . ($new['lantai'] ?? '-') . '<br>' .
                        '<strong>Ruangan:</strong> ' . ($new['ruangan'] ?? '-') . '<br>' .
                        '<strong>Detail:</strong> ' . ($new['detail'] ?? '-') .
                        '</div>';
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
                ->rawColumns(['from', 'to', 'status'])
                ->make(true);
        }

        $assets = Asset::whereHas('sekolah', function ($query) use ($user) {
            $query->where('kecamatan_id', $user->kecamatan_id);
        })->get();

        $schools = Sekolah::where('kecamatan_id', $user->kecamatan_id)->get();

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
        $rules = [
            'type' => 'required|in:mutation,loan,disposal',
            'asset_id' => 'required|exists:assets,id',
        ];

        if ($request->type === 'disposal') {
            $rules['jenis'] = 'required|in:lelang,hilang,musnah';
            $rules['keterangan'] = 'nullable';
        } else {
            $rules['keterangan'] = 'nullable';
        }

        // Validasi bersyarat untuk pengajuan loan
        if ($request->type === 'loan') {
            $rules['sekolah_id'] = 'required|exists:sekolahs,id';
            $rules['gedung'] = 'required';
            $rules['lantai'] = 'required';
            $rules['ruangan'] = 'required';
            $rules['detail'] = 'required';
            $rules['keterangan'] = 'nullable'; // Keterangan tidak wajib untuk loan
        } elseif ($request->type === 'disposal') {
            $rules['jenis'] = 'required|in:lelang,hilang,musnah';
            $rules['keterangan'] = 'nullable'; // Keterangan tidak wajib untuk disposal
        } else {
            $rules['keterangan'] = 'nullable'; // Keterangan tidak wajib untuk mutation
        }

        $request->validate($rules);

        $asset = Asset::find($request->asset_id);
        $user = Auth::user();

        // Validasi role
        if ($user->role_id != 1) {
            if ($asset->sekolah->kecamatan_id != $user->kecamatan_id) {
                return back()->withErrors(['asset_id' => 'Anda tidak memiliki izin untuk mengajukan peminjaman aset ini.']);
            }
        }

        $type = $request->type;
        $payload = [];

        if ($type === 'mutation') {
            $payload = [
                'from' => [
                    'nip_pic' => $request->old_nip,
                    'nama_pic' => $request->old_nama,
                    'jabatan_pic' => $request->old_jabatan,
                    'telp_pic' => $request->old_telp,
                ],
                'to' => [
                    'nip_pic' => $request->new_nip,
                    'nama_pic' => $request->new_nama,
                    'jabatan_pic' => $request->new_jabatan,
                    'telp_pic' => $request->new_telp,
                ],
                'detail' => $request->detail,
                'keterangan' => $request->input('keterangan', null), // Keterangan tidak wajib
            ];
        }

        if ($type === 'loan') {
            // Ambil data lokasi lama dari asset
            $oldValues = [
                'sekolah_id' => $asset->sekolah_id,
                'gedung' => $asset->gedung,
                'lantai' => $asset->lantai,
                'ruangan' => $asset->ruangan,
                'detail' => $asset->detail,
            ];

            $payload = [
                'old_values' => $oldValues,
                'new_values' => [
                    'sekolah_id' => $request->sekolah_id,
                    'gedung' => $request->gedung,
                    'lantai' => $request->lantai,
                    'ruangan' => $request->ruangan,
                    'detail' => $request->detail,
                ],
                'keterangan' => $request->input('keterangan', null), // Keterangan tidak wajib
            ];
        }

        if ($type === 'disposal') {
            $payload = [
                'jenis' => $request->jenis,
                'keterangan' => $request->input('keterangan', null), // Keterangan tidak wajib
            ];
        }

        Approval::create([
            'asset_id' => $request->asset_id,
            'type' => $type,
            'payload' => json_encode($payload),
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        if ($type === 'disposal') {
            return redirect()->route('asset.disposal')->with([
                'pesan' => 'Pengajuan disposal berhasil',
                'level-alert' => 'alert-success',
            ]);
        } elseif ($type === 'loan') {
            return redirect()->route('asset.loan')->with([
                'pesan' => 'Pengajuan peminjaman berhasil',
                'level-alert' => 'alert-success',
            ]);
        } elseif ($type === 'mutation') {
            return redirect()->route('asset.mutation')->with([
                'pesan' => 'Pengajuan mutasi berhasil',
                'level-alert' => 'alert-success',
            ]);
        }
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
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:approvals,id',
        ]);

        foreach ($request->ids as $id) {
            $approval = Approval::with('asset')->find($id);
            if (!$approval || $approval->status !== 'pending') continue;

            $payload = json_decode($approval->payload, true);
            $userId = Auth::id();
            $asset = $approval->asset;

            try {
                switch ($approval->type) {
                    case 'mutation':
                        $fields = ['nip_pic', 'nama_pic', 'jabatan_pic', 'telp_pic'];

                        // Convert payload ke array jika perlu
                        $oldValues = $asset->only($fields);
                        $newValues = array_intersect_key($payload['to'], array_flip($fields)); // Ambil nilai dari 'to'

                        // Update data asset
                        $asset->fill($newValues);
                        $asset->save();

                        History::create([
                            'asset_id' => $asset->id,
                            'user_id' => $userId,
                            'change_type' => 'mutation',
                            'changed_fields' => json_encode(array_keys($newValues)),
                            'old_values' => json_encode($oldValues),
                            'new_values' => json_encode($newValues),
                        ]);
                        break;

                    case 'loan':
                        $fields = ['sekolah_id', 'gedung', 'lantai', 'ruangan', 'detail'];

                        $oldValues = $asset->only($fields);
                        $newValues = array_intersect_key($payload['new_values'], array_flip($fields));

                        $asset->fill($newValues);
                        $asset->save();

                        History::create([
                            'asset_id' => $asset->id,
                            'user_id' => $userId,
                            'change_type' => 'location',
                            'changed_fields' => json_encode(array_keys($newValues)),
                            'old_values' => json_encode($oldValues),
                            'new_values' => json_encode($newValues),
                        ]);
                        break;

                    case 'disposal':
                        $keterangan = $payload['keterangan'] ?? '-';
                        $jenis = $payload['jenis'] ?? '-';

                        $oldValues = $asset->toArray();
                        $changedFields = array_keys($oldValues); // Ambil semua key sebagai field yang berubah

                        History::create([
                            'asset_id' => null, // Tidak menampilkan asset_id
                            'user_id' => $userId,
                            'change_type' => 'disposal',
                            'changed_fields' => json_encode($changedFields), // Semua data aset dimasukkan ke changed_fields
                            'old_values' => json_encode($oldValues),
                            'new_values' => json_encode([
                                'jenis' => $jenis,
                                'keterangan' => $keterangan,
                            ]),
                        ]);

                        if ($asset->tag) {
                            $tag = Tag::where('rfid_number', $asset->rfid_number)->first();
                            if ($tag) {
                                $tag->update(['status' => 'available']);
                            }
                        }

                        if ($asset->foto_awal) {
                            Storage::disk('public')->delete('assets/' . $asset->foto_awal);
                        }

                        if ($asset->foto_kondisi) {
                            Storage::disk('public')->delete('assets/' . $asset->foto_kondisi);
                        }

                        $asset->delete();
                        break;
                }

                $approval->update(['status' => 'approved']);
            } catch (\Exception $e) {
                Log::error("Approval failed for ID {$id}: {$e->getMessage()}");
            }
        }

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
