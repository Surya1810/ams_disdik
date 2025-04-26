<?php

namespace App\Http\Controllers;

use App\Models\Approval;
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
                ->where('change_type', 'attribute') // kamu bisa ubah ini kalau mau tampilkan semua tipe
                ->orderBy('created_at', 'desc');

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset?->name ?? '-')
                ->addColumn('user', fn($row) => $row->user?->name ?? 'System')
                ->editColumn('perubahan', function ($row) {
                    if ($row->change_type == 'create') {
                        return "<span class='text-success'>Asset telah ditambahkan</span>";
                    }

                    if ($row->change_type == 'update') {
                        $fields = $row->changed_fields ?? [];
                        $old = $row->old_values ?? [];
                        $new = $row->new_values ?? [];

                        if (empty($fields)) {
                            return "<span class='text-warning'>Asset diubah, namun tidak ada perubahan terdeteksi</span>";
                        }

                        $result = "<span class='text-primary'>Asset telah diubah:</span><br>";
                        foreach ($fields as $key) {
                            $oldVal = $old[$key] ?? '-';
                            $newVal = $new[$key] ?? '-';
                            $result .= "<div><strong>" . ucfirst($key) . ":</strong> <span class='text-danger'>$oldVal</span> → <span class='text-success'>$newVal</span></div>";
                        }

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
            $data = History::with(['asset', 'user', 'approval'])
                ->where('change_type', 'disposal')
                ->where('user_id', $user->id) // Filter history berdasarkan user yang login
                ->leftJoin('assets', 'histories.asset_id', '=', 'assets.id')
                ->orderBy('histories.created_at', 'desc')
                ->select([
                    'histories.*',
                    'assets.name as asset_name'
                ]);

            return DataTables::of($data)
                ->addColumn('asset', function ($row) {
                    return $row->asset->name ?? $row->asset_name ?? '-'; // Cek relasi asset, lalu kolom asset_name
                })
                ->addColumn('user', function ($row) use ($user) {
                    return $row->user->name ?? $user->name ?? '-'; // default user login jika tidak ada relasi
                })
                ->addColumn('jenis', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    return $newValues['jenis'] ?? '-';
                })
                ->addColumn('keterangan', function ($row) {
                    $newValues = json_decode($row->new_values, true);
                    return $newValues['keterangan'] ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y H:i');
                })
                ->addColumn('status', function ($row) {
                    return $row->approval->status ?? '-';
                })
                ->rawColumns(['status']) // Jika ada HTML di kolom status
                ->make(true);
        }

        return view('asset.disposal');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(History $history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, History $history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(History $history)
    {
        //
    }
}
