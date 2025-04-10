<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Approval::with(['asset', 'user'])->latest();

            if ($request->has('status') && $request->status !== null) {
                $data->where('status', $request->status);
            }

            if ($request->has('type') && $request->type !== null) {
                $data->where('type', $request->type);
            }

            return DataTables::of($data)
                ->addColumn('asset', fn($row) => $row->asset->name)
                ->addColumn('user', fn($row) => $row->user->name)
                ->addColumn('keterangan', function ($row) {
                    return $row->payload['keterangan'] ?? '-';
                })
                ->addColumn('aksi', function ($row) {
                    return view('approval.partials.actions', compact('row'))->render();
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('approval.index');
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

    public function approve(Approval $request)
    {
        $approval = $request;
        $approval->update(['status' => 'approved']);

        $asset = $approval->asset;
        $payload = $approval->payload;
        $userId = Auth::user()->id;

        if ($approval->type === 'mutation') {
            $old = $asset->only(['nip_pic', 'nama_pic', 'jabatan_pic', 'telp_pic']);
            $asset->update($payload);
            History::create([
                'asset_id' => $asset->id,
                'type' => 'mutation',
                'old_value' => $old,
                'new_value' => $payload,
                'created_by' => $userId,
            ]);
        }

        if ($approval->type === 'loan') {
            $old = $asset->only(['sekolah_id', 'gedung', 'lantai', 'ruangan', 'detail']);
            $asset->update($payload);
            History::create([
                'asset_id' => $asset->id,
                'type' => 'location',
                'old_value' => $old,
                'new_value' => $payload,
                'created_by' => $userId,
            ]);
        }

        if ($approval->type === 'disposal') {
            $asset->delete();
        }

        return response()->json(['message' => 'Approved and updated successfully.']);
    }

    public function reject(Approval $request, Request $input)
    {
        $request->update([
            'status' => 'rejected',
            'rejection_note' => $input->rejection_note
        ]);
        return response()->json(['message' => 'Approval request rejected.']);
    }

    public function bulkApprove(Request $request)
    {
        foreach ($request->ids as $id) {
            $approval = Approval::find($id);
            if ($approval && $approval->status === 'pending') {
                $this->approve($approval);
            }
        }
        return response()->json(['message' => 'Semua permintaan telah disetujui.']);
    }

    public function bulkReject(Request $request)
    {
        Approval::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejection_note' => $request->rejection_note
            ]);
        return response()->json(['message' => 'Semua permintaan telah ditolak.']);
    }
}
