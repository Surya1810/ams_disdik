<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\History;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

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
                ->latest();

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name)
                ->addColumn('user', fn($row) => $row->user->name)
                ->addColumn('perubahan', function ($row) {
                    $changes = '';
                    foreach (json_decode($row->old_values, true) as $key => $old) {
                        $new = json_decode($row->new_values, true)[$key] ?? '';
                        $changes .= "<div><strong>$key:</strong> $old &rarr; $new</div>";
                    }
                    return $changes;
                })
                ->editColumn('histories.created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->rawColumns(['perubahan'])
                ->make(true);
        }

        return view('history.changes');
    }

    public function mutationHistory(Request $request)
    {
        if ($request->ajax()) {
            $data = History::with(['asset', 'user', 'approvalRequest'])
                ->where('change_type', 'mutation')
                ->latest();

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name)
                ->addColumn('user', fn($row) => $row->user->name)
                ->addColumn('dari', function ($row) {
                    return implode(', ', array_map(
                        fn($k, $v) => "$k: $v",
                        array_keys(json_decode($row->old_values, true)),
                        json_decode($row->old_values, true)
                    ));
                })
                ->addColumn('ke', function ($row) {
                    return implode(', ', array_map(
                        fn($k, $v) => "$k: $v",
                        array_keys(json_decode($row->new_values, true)),
                        json_decode($row->new_values, true)
                    ));
                })
                ->addColumn('status', function ($row) {
                    return $row->approvalRequest->status ?? '-';
                })
                ->editColumn('histories.created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->make(true);
        }

        return view('history.mutation');
    }

    public function locationHistory(Request $request)
    {
        if ($request->ajax()) {
            $data = History::with(['asset', 'user'])
                ->where('change_type', 'location')
                ->latest();

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name)
                ->addColumn('user', fn($row) => $row->user->name)
                ->addColumn('dari', function ($row) {
                    return implode(', ', array_map(
                        fn($k, $v) => "$k: $v",
                        array_keys(json_decode($row->old_values, true)),
                        json_decode($row->old_values, true)
                    ));
                })
                ->addColumn('ke', function ($row) {
                    return implode(', ', array_map(
                        fn($k, $v) => "$k: $v",
                        array_keys(json_decode($row->new_values, true)),
                        json_decode($row->new_values, true)
                    ));
                })
                ->editColumn('histories.created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->make(true);
        }

        return view('history.location');
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
