<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('kecamatan')->where('id', '!=', 1)->get();

            return DataTables::of($users)
                ->addColumn('kecamatan', function ($user) {
                    return $user->kecamatan ? $user->kecamatan->name : '-';
                })
                ->addColumn('sekolahs_count', function ($user) {
                    return \App\Models\Sekolah::where('kecamatan_id', $user->kecamatan_id)->count();
                })
                ->addColumn('assets_count', function ($user) {
                    return \App\Models\Asset::whereHas('sekolah', function ($query) use ($user) {
                        $query->where('kecamatan_id', $user->kecamatan_id);
                    })->count();
                })
                ->addColumn('action', function ($row) {
                    return '
                <a href="javascript:void(0)" class="text-primary px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"
                    onclick="editPengguna(' . $row->id . ', \'' . addslashes($row->name) . '\')">
                    <i class="fa-solid fa-pencil"></i>
                </a>
                <a href="javascript:void(0)" class="text-danger px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                    onclick="deletePengguna(' . $row->id . ')">
                    <i class="fa-solid fa-trash"></i>
                </a>
                <form id="delete-form-' . $row->id . '" action="' . route('user.destroy', $row->id) . '" method="POST" style="display: none;">
                    ' . csrf_field() . method_field('DELETE') . '
                </form>
                    ';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        $kecamatans = Kecamatan::all();
        $roles = Role::whereIn('id', [2, 3])->get();
        return view('user.index', compact('kecamatans', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'role' => 'required|in:1,2,3',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:password'
        ]);

        User::create([
            'name' => $request->name,
            'role_id' => $request->role,
            'kecamatan_id' => $request->kecamatan_id,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with(['pesan' => 'Pengguna berhasil ditambahkan', 'level-alert' => 'alert-success']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required',
            'kecamatan_id' => 'required',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8';
            $rules['confirm_password'] = 'required|string|same:password';
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->role_id = $validated['role'];
        $user->kecamatan_id = $validated['kecamatan_id'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('user.index')->with(['pesan' => 'Pengguna berhasil diperbarui', 'level-alert' => 'alert-success']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findorfail($id);

        if ($user->document == null) {
            $user->delete();
            return redirect()->back()->with(['pesan' => 'User deleted successfully', 'level-alert' => 'alert-success']);
        } else {
            return redirect()->back()->with(['pesan' => 'User has document', 'level-alert' => 'alert-danger']);
        }
    }
}
