<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check() || Auth::user()->role_id == 3) {
            abort(403, 'Unauthorized');
        } else {
            if ($request->ajax()) {
                if (auth()->role_id == 1) {
                    $users = User::with('kecamatan');

                    return DataTables::of($users)
                        ->filter(function ($query) use ($request) {
                            if (!empty($request->search['value'])) {
                                $search = $request->search['value'];
                                $query->where('name', 'like', "%{$search}%");
                            }
                        })
                        ->addColumn('action', function ($row) {
                            return '
                            <a role="button" class="text-danger px-3 mb-0 border-radius-lg"
                                onclick="deleteUser(' . $row->id . ')"><i class="fa-solid fa-trash"></i></a>
                            <form id="delete-form-' . $row->id . '" 
                                action="' . route('user.destroy', $row->id) . '" 
                                method="POST" style="display: none;">
                                ' . csrf_field() . method_field('DELETE') . '
                            </form>
                        ';
                        })
                        ->rawColumns(['action']) // Izinkan HTML dalam kolom action
                        ->make(true);
                }
                if (auth()->role_id == 2) {
                    $users = User::with('kecamatan')
                        ->whereNot('id', 1);

                    return DataTables::of($users)
                        ->filter(function ($query) use ($request) {
                            if (!empty($request->search['value'])) {
                                $search = $request->search['value'];
                                $query->where('name', 'like', "%{$search}%");
                            }
                        })
                        ->addColumn('action', function ($row) {
                            return '
                            <a role="button" class="text-danger px-3 mb-0 border-radius-lg"
                                onclick="deletePengguna(' . $row->id . ')"><i class="fa-solid fa-trash"></i></a>
                            <form id="delete-form-' . $row->id . '" 
                                action="' . route('user.destroy', $row->id) . '" 
                                method="POST" style="display: none;">
                                ' . csrf_field() . method_field('DELETE') . '
                            </form>
                        ';
                        })
                        ->rawColumns(['action']) // Izinkan HTML dalam kolom action
                        ->make(true);
                }
            }
            return view('user.index');
        }
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
            'name' => 'bail|required|max:255|unique:users,name',
            'role' => 'bail|required',
            'password' => 'required|string|min:8',
        ]);

        $old = session()->getOldInput();

        $user = new User();
        $user->name = $request->name;
        $user->role_id = $request->role;
        $user->password = Hash::make($request['password']);
        $user->save();

        return redirect()->route('user.index')->with(['pesan' => 'User created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findorfail($id);
        $request->validate([
            'name' => 'bail|required|max:255|unique:users,name,' . $user->id,
            'role_update' => 'bail|required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $old = session()->getOldInput();

        $user->name = $request->name;
        $user->role_id = $request->role_update;
        $user->password = Hash::make($request['password']);
        $user->update();

        return redirect()->route('user.index')->with(['pesan' => 'User updated successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findorfail($id);

        if ($user->document == null) {
            $user->delete();
            return redirect()->back()->with(['pesan' => 'User deleted successfully', 'level-alert' => 'alert-danger']);
        } else {
            return redirect()->back()->with(['pesan' => 'User has document', 'level-alert' => 'alert-danger']);
        }
    }
}
