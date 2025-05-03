<?php

namespace App\Http\Controllers;

use App\Models\History;
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
            $data = History::with(['asset', 'user'])
                ->where('change_type', 'attribute')
                ->where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset?->name ?? '-')
                ->addColumn('user', fn($row) => $row->user?->name ?? 'System')
                ->editColumn('perubahan', function ($row) {
                    if ($row->change_type == 'create') {
                        return "<span class='text-success'>Asset telah ditambahkan</span>";
                    }

                    if (in_array($row->change_type, ['update', 'attribute'])) {
                        $oldValues = json_decode($row->old_values, true) ?? [];
                        $newValues = json_decode($row->new_values, true) ?? [];
                        $changedFields = json_decode($row->changed_fields, true) ?? [];

                        if (empty($changedFields)) {
                            return "<span class='text-warning'>Asset diubah, namun tidak ada perubahan terdeteksi</span>";
                        }

                        $result = "<div class='text-primary mb-2'>Asset telah diubah:</div>";
                        $result .= "<ul style='padding-left: 18px;'>"; // Biar lebih rapi masuk ke dalam
                        foreach ($changedFields as $field) {
                            $oldVal = $oldValues[$field] ?? '-';
                            $newVal = $newValues[$field] ?? '-';
                            $result .= "<li><strong>" . ucfirst($field) . ":</strong> dari <em>$oldVal</em> ke <em>$newVal</em></li>";
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
            $data = History::with(['asset', 'user', 'approval'])
                ->where('change_type', 'mutation')
                ->leftJoin('assets', 'histories.asset_id', '=', 'assets.id')
                ->orderBy('histories.created_at', 'desc')
                ->select('histories.*', 'assets.name as asset_name');

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('dari', function ($row) {
                    // Menangani JSON kosong atau tidak valid
                    $oldValues = json_decode($row->old_values, true);
                    if (is_array($oldValues) && !empty($oldValues)) {
                        return '<ul>' . collect($oldValues)->map(fn($v, $k) => "<li><strong>$k</strong>: $v</li>")->implode('') . '</ul>';
                    }
                    return '-'; // Menangani jika old_values kosong atau tidak valid
                })
                ->addColumn('ke', function ($row) {
                    // Menangani JSON kosong atau tidak valid
                    $newValues = json_decode($row->new_values, true);
                    if (is_array($newValues) && !empty($newValues)) {
                        return '<ul>' . collect($newValues)->map(fn($v, $k) => "<li><strong>$k</strong>: $v</li>")->implode('') . '</ul>';
                    }
                    return '-'; // Menangani jika new_values kosong atau tidak valid
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->rawColumns(['dari', 'ke'])  // Supaya HTML di 'dari' dan 'ke' tidak di-escape
                ->make(true);
        }

        return view('history.mutation');
    }

    public function locationHistory(Request $request)
    {
        if ($request->ajax()) {
            $data = History::with(['asset', 'user'])
                ->where('change_type', 'location')
                ->leftJoin('assets', 'histories.asset_id', '=', 'assets.id')
                ->orderBy('histories.created_at', 'desc')
                ->select('histories.*', 'assets.name as asset_name');

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name ?? '-')
                ->addColumn('user', fn($row) => $row->user->name ?? '-')
                ->addColumn('dari', function ($row) {
                    $oldValues = json_decode($row->old_values, true);
                    if (is_array($oldValues) && !empty($oldValues)) {
                        return '<ul>' . collect($oldValues)->map(fn($v, $k) => "<li><strong>$k</strong>: $v</li>")->implode('') . '</ul>';
                    }
                    return '-';
                })
                ->addColumn('ke', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    if (is_array($newValues) && !empty($newValues)) {
                        return '<ul>' . collect($newValues)->map(fn($v, $k) => "<li><strong>$k</strong>: $v</li>")->implode('') . '</ul>';
                    }
                    return '-';
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
            $data = History::with(['asset', 'user']) // ambil relasi asset dan user
                ->where('change_type', 'disposal')
                ->where('user_id', $user->id)
                ->latest();

            return DataTables::of($data)
                ->addColumn('keterangan', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    return $newValues['keterangan'] ?? '-';
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? '-';
                })
                ->addColumn('jenis', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    $jenis = $newValues['jenis'] ?? '-';
                    $badge = $jenis === 'lelang' ? 'warning' : ($jenis === 'hilang' ? 'danger' : 'secondary');
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($jenis) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->rawColumns(['jenis'])
                ->make(true);
        }

        return view('history.disposal');
    }
}
