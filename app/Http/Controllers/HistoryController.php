<?php

namespace App\Http\Controllers;

use App\Models\History;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function changesHistory(Request $request)
    {
        if ($request->ajax()) {
            $baseQuery = History::with(['asset', 'user'])
                ->where('change_type', 'attribute')
                ->orderBy('histories.created_at', 'desc');

            $query = Auth::user()->role_id == 2
                ? $baseQuery
                : $baseQuery->where('requester_id', Auth::user()->id);

            return DataTables::of($query)
                ->addColumn('rfid_number', fn($row) => $row->asset?->rfid_number ?? '-')
                ->addColumn('kode', fn($row) => $row->asset?->kode ?? '-')
                ->addColumn('asset', fn($row) => $row->asset?->name ?? '-')
                ->addColumn('user', fn($row) => $row->user?->name ?? '-')
                ->editColumn('perubahan', function ($row) {
                    if ($row->change_type == 'create') {
                        return "<span class='text-success'>Asset telah ditambahkan</span>";
                    }

                    if (in_array($row->change_type, ['update', 'attribute'])) {
                        $oldValues = json_decode($row->old_values, true) ?? [];
                        $newValues = json_decode($row->new_values, true) ?? [];
                        $changedFields = json_decode($row->changed_fields, true) ?? [];

                        // Hapus perubahan updated_at
                        $changedFields = array_filter($changedFields, fn($field) => $field !== 'updated_at');

                        if (empty($changedFields)) {
                            return "<span class='text-warning'>Asset diubah, namun tidak ada perubahan terdeteksi</span>";
                        }

                        $result = "<div class='text-primary mb-2'>Asset telah diubah:</div>";
                        $result .= "<ul style='padding-left: 18px;'>";

                        foreach ($changedFields as $field) {
                            $oldVal = $oldValues[$field] ?? '-';
                            $newVal = $newValues[$field] ?? '-';

                            // Coba format kalau nilainya mirip tanggal
                            try {
                                if (strtotime($oldVal)) {
                                    $oldVal = Carbon::parse($oldVal)->translatedFormat('d F Y');
                                }
                                if (strtotime($newVal)) {
                                    $newVal = Carbon::parse($newVal)->translatedFormat('d F Y');
                                }
                            } catch (\Exception $e) {
                                // Lewat saja kalau gagal parsing
                            }

                            $label = ucwords(str_replace('_', ' ', $field));
                            $result .= "<li><strong>$label:</strong> dari <em>" . e($oldVal) . "</em> ke <em>" . e($newVal) . "</em></li>";
                        }

                        $result .= "</ul>";
                        return $result;
                    }

                    return "<span class='text-muted'>Tidak ada perubahan</span>";
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->rawColumns(['perubahan'])
                ->make(true);
        }

        return view('history.changes');
    }

    public function mutationHistory(Request $request)
    {
        if ($request->ajax()) {
            $baseQuery = History::with(['asset', 'user', 'approval'])
                ->where('change_type', 'mutation')
                ->leftJoin('assets', 'histories.asset_id', '=', 'assets.id')
                ->orderBy('histories.created_at', 'desc')
                ->select('histories.*', 'assets.name as asset_name');

            $query = Auth::user()->role_id == 2
                ? $baseQuery
                : $baseQuery->where('requester_id', Auth::user()->id);

            return DataTables::of($query)
                ->addColumn('rfid_number', fn($row) => $row->asset->rfid_number ?? '-')
                ->addColumn('kode', fn($row) => $row->asset->kode ?? '-')
                ->addColumn('asset', fn($row) => $row->asset->name ?? '-')
                ->addColumn('user', function ($row) {
                    if (Auth::user()->role_id == 2) {
                        $user = json_decode($row->requester_payload, true)['name'];
                        return $user;
                    }
                    return $row->user->name ?? '-';
                })
                ->addColumn('dari', function ($row) {
                    $oldValues = json_decode($row->old_values, true);

                    // Mapping field ke label ramah
                    $labelMap = [
                        'nip_pic' => 'NIP',
                        'nama_pic' => 'Nama',
                        'jabatan_pic' => 'Jabatan',
                        'telp_pic' => 'Telp',
                    ];

                    if (is_array($oldValues) && !empty($oldValues)) {
                        return '<ul>' . collect($oldValues)->map(function ($v, $k) use ($labelMap) {
                            $label = $labelMap[$k] ?? ucwords(str_replace('_', ' ', $k));
                            return "<li><strong>{$label}</strong>: {$v}</li>";
                        })->implode('') . '</ul>';
                    }
                    return '-';
                })
                ->addColumn('ke', function ($row) {
                    $newValues = json_decode($row->new_values, true);

                    // Mapping field ke label ramah
                    $labelMap = [
                        'nip_pic' => 'NIP',
                        'nama_pic' => 'Nama',
                        'jabatan_pic' => 'Jabatan',
                        'telp_pic' => 'Telp',
                    ];

                    if (is_array($newValues) && !empty($newValues)) {
                        return '<ul>' . collect($newValues)->map(function ($v, $k) use ($labelMap) {
                            $label = $labelMap[$k] ?? ucwords(str_replace('_', ' ', $k));
                            return "<li><strong>{$label}</strong>: {$v}</li>";
                        })->implode('') . '</ul>';
                    }
                    return '-';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->rawColumns(['dari', 'ke'])
                ->make(true);
        }

        return view('history.mutation');
    }

    public function locationHistory(Request $request)
    {
        if ($request->ajax()) {
            $baseQuery = History::with(['asset', 'user'])
                ->where('change_type', 'location')
                ->leftJoin('assets', 'histories.asset_id', '=', 'assets.id')
                ->orderBy('histories.created_at', 'desc')
                ->select('histories.*', 'assets.name as asset_name');

            $query = Auth::user()->role_id == 2
                ? $baseQuery
                : $baseQuery->where('requester_id', Auth::user()->id);

            return DataTables::of($query)
                ->addColumn('rfid_number', fn($row) => $row->asset->rfid_number ?? '-')
                ->addColumn('kode', fn($row) => $row->asset->name ?? '-')
                ->addColumn('asset', fn($row) => $row->asset->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('dari', function ($row) {
                    $oldValues = json_decode($row->old_values, true);

                    // Ambil nama sekolah jika ada
                    if (isset($oldValues['sekolah_id'])) {
                        $sekolah = \App\Models\Sekolah::find($oldValues['sekolah_id']);
                        $oldValues['sekolah'] = $sekolah->name ?? '-';
                        unset($oldValues['sekolah_id']);
                    }

                    // Urutan field yang diinginkan
                    $orderedKeys = ['sekolah', 'gedung', 'lantai', 'ruangan', 'detail'];

                    $output = '<ul>';
                    foreach ($orderedKeys as $key) {
                        if (isset($oldValues[$key])) {
                            $label = ucfirst($key);
                            $output .= "<li><strong>{$label}</strong>: {$oldValues[$key]}</li>";
                        }
                    }
                    $output .= '</ul>';

                    return $output === '<ul></ul>' ? '-' : $output;
                })
                ->addColumn('ke', function ($row) {
                    $newValues = json_decode($row->new_values, true);

                    if (isset($newValues['sekolah_id'])) {
                        $sekolah = \App\Models\Sekolah::find($newValues['sekolah_id']);
                        $newValues['sekolah'] = $sekolah->name ?? '-';
                        unset($newValues['sekolah_id']);
                    }

                    $orderedKeys = ['sekolah', 'gedung', 'lantai', 'ruangan', 'detail'];

                    $output = '<ul>';
                    foreach ($orderedKeys as $key) {
                        if (isset($newValues[$key])) {
                            $label = ucfirst($key);
                            $output .= "<li><strong>{$label}</strong>: {$newValues[$key]}</li>";
                        }
                    }
                    $output .= '</ul>';

                    return $output === '<ul></ul>' ? '-' : $output;
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->rawColumns(['dari', 'ke'])
                ->make(true);
        }

        return view('history.location');
    }

    public function disposalHistory(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $baseQuery = History::with(['asset', 'user', 'approval']) // ambil relasi asset dan user
                ->where('change_type', 'disposal');

            $query = $user->role_id == 2
                ? $baseQuery
                : $baseQuery->where('requester_id', $user->id);

            return DataTables::of($query)
                ->addColumn('rfid_number', function ($row) {
                    if ($row->asset) {
                        return $row->asset?->rfid_number;
                    }
                    /**
                     * ngambil dari old values karena
                     * aset sudah dihapus dari tabel (disposal diterima)
                     */
                    $oldValues = json_decode($row->old_values, true);
                    $rfidNumber = $oldValues['rfid_number'];
                    return $rfidNumber;
                })
                ->addColumn('kode', function ($row) {
                    if ($row->asset) {
                        return $row->asset?->kode;
                    }
                    /**
                     * ngambil dari old values karena
                     * aset sudah dihapus dari tabel (disposal diterima)
                     */
                    $oldValues = json_decode($row->old_values, true);
                    $kode = $oldValues['kode'];
                    return $kode;
                })
                ->addColumn('keterangan', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    return $newValues['keterangan'] ?? ($row->approval->rejection_note ?? '-');
                })
                ->addColumn('user', function ($row) {
                    /**
                     * User sebelumnya yang diambil malah user yang approve atau tolak
                     * Karena di tabel kolomnya diajukan, maka diubah ke requester_payload
                     */
                    $requester = json_decode($row->requester_payload, true);
                    return isset($requester['name']) ? $requester['name'] : '-';
                })
                ->addColumn('jenis', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    $jenis = $newValues['jenis'] ?? 'lelang';
                    $badge = $jenis === 'lelang' ? 'warning' : 'secondary';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($jenis) . '</span>';
                })
                ->addColumn('approval', function ($row) {
                    /**
                     * Jika status tidak ditemukan di approval
                     * itu berarti disposal diterima dan aset dihapus
                     */
                    $statusBadge = isset($row->approval->status) ? 'danger' : 'success';
                    $statusText = isset($row->approval->status) ? 'Rejected' : 'Approved';
                    return '<span class="badge bg-' . $statusBadge . '">' . $statusText . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->filterColumn('rfid_number', function ($query, $keyword) {
                    $query->where('old_values', 'like', "%{$keyword}%");
                })
                ->filterColumn('kode', function ($query, $keyword) {
                    $query->where('old_values', 'like', "%{$keyword}%");
                })
                ->rawColumns(['jenis', 'approval'])
                ->make(true);
        }

        return view('history.disposal');
    }
}
