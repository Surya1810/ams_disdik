<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\Asset;
use App\Models\History;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Tag;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Approval::with(['asset', 'requester'])
                ->join('assets', 'approvals.asset_id', '=', 'assets.id')
                ->join('users as u', 'approvals.requested_by', '=', 'u.id')
                ->select('approvals.*')
                ->orderBy('approvals.created_at', 'desc');

            if ($request->filled('status')) {
                $data->where('approvals.status', $request->status);
            }

            if ($request->filled('type')) {
                $data->where('approvals.type', $request->type);
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
                    $query->where('assets.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('asset', function ($query, $order) {
                    $query->orderBy('assets.name', $order);
                })
                ->addColumn('requester', fn($row) => $row->requester->name ?? '-')
                ->filterColumn('requester', function ($query, $keyword) {
                    $query->where('u.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('requester', function ($query, $order) {
                    $query->orderBy('u.name', $order);
                })
                ->addColumn('keterangan', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['keterangan'] ?? '-';
                })
                ->filterColumn('keterangan', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
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
                ->addColumn('waktu', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)
                        ->locale('id')->translatedFormat('d F Y H:i');
                })
                ->addColumn('rejection_note', fn($row) => $row->rejection_note ?? '-')
                ->rawColumns(['checkbox', 'status', 'waktu'])
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
                ->addColumn('id', fn($row) => $row->id)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $from = $payload['from'] ?? null;
                    if ($from) {
                        return implode('#', [
                            $from['nip_pic'] ?? '',
                            $from['nama_pic'] ?? '',
                            $from['jabatan_pic'] ?? '',
                            $from['telp_pic'] ?? '',
                        ]);
                    }
                    return '-';
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $to = $payload['to'] ?? null;
                    if ($to) {
                        return implode('#', [
                            $to['nip_pic'] ?? '',
                            $to['nama_pic'] ?? '',
                            $to['jabatan_pic'] ?? '',
                            $to['telp_pic'] ?? '',
                        ]);
                    }
                    return '-';
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
                ->filterColumn('asset_name', function ($query, $keyword) {
                    $query->whereHas('asset', function ($q) use ($keyword) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%']);
                    });
                })
                ->filterColumn('requested_by', function ($query, $keyword) {
                    $query->whereHas('requester', function ($q) use ($keyword) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%']);
                    });
                })
                ->filterColumn('from', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('to', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                ->filterColumn('requested_at', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        $user = Auth::user();
        $assets = $user->role == 'admin'
            ? Asset::all()
            : Asset::whereHas('sekolah', function ($query) use ($user) {
                $query->where('kecamatan_id', $user->kecamatan_id);
            })->get();

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
                ->addColumn('id', fn($row) => $row->id)
                ->addColumn('asset_name', fn($row) => $row->asset->name ?? '-')
                ->addColumn('requested_by', fn($row) => $row->requester->name ?? '-')
                ->addColumn('from', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $old = $payload['old_values'] ?? [];

                    $sekolah = \App\Models\Sekolah::find($old['sekolah_id'] ?? null);
                    $sekolahName = $sekolah->name ?? '-';

                    return implode('#', [
                        $sekolahName,
                        $old['gedung'] ?? '-',
                        $old['lantai'] ?? '-',
                        $old['ruangan'] ?? '-',
                        $old['detail'] ?? '-'
                    ]);
                })
                ->addColumn('to', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $new = $payload['new_values'] ?? [];

                    $sekolah = \App\Models\Sekolah::find($new['sekolah_id'] ?? null);
                    $sekolahName = $sekolah->name ?? '-';

                    return implode('#', [
                        $sekolahName,
                        $new['gedung'] ?? '-',
                        $new['lantai'] ?? '-',
                        $new['ruangan'] ?? '-',
                        $new['detail'] ?? '-'
                    ]);
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
                ->filterColumn('asset_name', function ($query, $keyword) {
                    $query->whereHas('asset', function ($q) use ($keyword) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%']);
                    });
                })
                ->filterColumn('requested_by', function ($query, $keyword) {
                    $query->whereHas('requester', function ($q) use ($keyword) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%']);
                    });
                })
                ->filterColumn('from', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('to', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where('status', 'like', "%{$keyword}%");
                })
                ->filterColumn('requested_at', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['status'])
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
        $user = Auth::user();

        if ($request->ajax()) {
            $data = Approval::with(['asset', 'requester'])
                ->where('type', 'disposal')
                ->whereHas('asset')
                ->where('requested_by', $user->id)
                ->select('approvals.*')
                ->latest();

            return DataTables::of($data)
                ->addColumn('id', fn($row) => $row->id)
                ->addIndexColumn()
                ->addColumn('keterangan', function ($row) {
                    $payload = json_decode($row->payload, true);
                    return $payload['keterangan'] ?? '-';
                })
                ->addColumn('jenis', function ($row) {
                    $payload = json_decode($row->payload, true);
                    $jenis = $payload['jenis'] ?? '-';
                    $badge = $jenis === 'lelang' ? 'warning' : ($jenis === 'hilang' ? 'danger' : 'secondary');
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($jenis) . '</span>';
                })
                ->addColumn('user', function ($row) {
                    return $row->requester->name ?? '-';
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
                ->addColumn('action', function ($row) {
                    $url = route('disposal.pdf', $row->id);
                    return '<a href="' . $url . '" title="Download PDF" style="color: #dc3545; text-decoration: none;">
                    <i class="fa-solid fa-file-pdf fa-lg"></i>
                </a>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })

                ->filterColumn('keterangan', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('jenis', function ($query, $keyword) {
                    $query->whereRaw('LOWER(payload) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->whereRaw("LOWER(status) LIKE ?", ["%" . strtolower($keyword) . "%"]);
                })

                ->rawColumns(['jenis', 'status', 'action'])
                ->make(true);
        }

        $assets = Asset::whereHas('sekolah', function ($query) use ($user) {
            $query->where('kecamatan_id', $user->kecamatan_id);
        })->get();

        return view('asset.disposal', compact('assets'));
    }

    public function mutationPdf($id)
    {
        Carbon::setLocale('id');
        $mutation = Approval::with('asset')->findOrFail($id);

        $pdf = Pdf::loadView('asset.mutation_pdf', compact('mutation'));
        return $pdf->download("berita_Acara_Mutasi_{$mutation->id}.pdf");
    }

    public function loanPdf($id)
    {
        Carbon::setLocale('id');
        $loan = Approval::with('asset')->findOrFail($id);

        $pdf = Pdf::loadView('asset.loan_pdf', compact('loan'));
        return $pdf->download("Berita_Acara_Pinjam_{$loan->id}.pdf");
    }

    public function disposalPdf($id)
    {
        Carbon::setLocale('id');
        $disposal = Approval::with('asset')->findOrFail($id);

        $payload = json_decode($disposal->payload, true);
        $jenis = $payload['jenis'] ?? 'disposal';

        // Nama file hanya berdasarkan jenis saja
        $fileName = "Berita_Acara_" . ucfirst($jenis) . ".pdf";

        $pdf = Pdf::loadView('asset.disposal_pdf', compact('disposal'))->setPaper('A4');
        return $pdf->download($fileName);
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

        if ($request->type === 'disposal' && $request->has('jenis')) {
            $request->merge([
                'jenis' => strtolower($request->jenis),
            ]);
        }

        if ($request->type === 'disposal') {
            $rules['jenis'] = 'required|in:lelang,hilang'; // <- perbaiki disini
            $rules['keterangan'] = 'nullable';
        } elseif ($request->type === 'loan') {
            $rules['sekolah_id'] = 'required|exists:sekolahs,id';
            $rules['gedung'] = 'required';
            $rules['lantai'] = 'required';
            $rules['ruangan'] = 'required';
            $rules['detail'] = 'required';
            $rules['keterangan'] = 'nullable'; // Tidak wajib
        } else { // mutation
            $rules['keterangan'] = 'nullable';
        }

        $request->validate($rules);

        $asset = Asset::findOrFail($request->asset_id);
        $user = Auth::user();

        // Validasi role user (jika bukan admin)
        if ($user->role_id != 1 && $asset->sekolah->kecamatan_id != $user->kecamatan_id) {
            return back()->withErrors(['asset_id' => 'Anda tidak memiliki izin untuk mengajukan aset ini.']);
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
                'keterangan' => $request->input('keterangan', null),
            ];
        } elseif ($type === 'loan') {
            $payload = [
                'old_values' => [
                    'sekolah_id' => $asset->sekolah_id,
                    'gedung' => $asset->gedung,
                    'lantai' => $asset->lantai,
                    'ruangan' => $asset->ruangan,
                    'detail' => $asset->detail,
                ],
                'new_values' => [
                    'sekolah_id' => $request->sekolah_id,
                    'gedung' => $request->gedung,
                    'lantai' => $request->lantai,
                    'ruangan' => $request->ruangan,
                    'detail' => $request->detail,
                ],
                'keterangan' => $request->input('keterangan', null),
            ];
        } elseif ($type === 'disposal') {
            $payload = [
                'jenis' => $request->jenis,
                'keterangan' => $request->input('keterangan', null),
            ];
        }

        Approval::create([
            'asset_id' => $request->asset_id,
            'type' => $type,
            'payload' => json_encode($payload),
            'status' => 'pending',
            'requested_by' => $user->id,
        ]);

        // Redirect sesuai type
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
