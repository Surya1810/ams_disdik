<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all()->except(1);
        $users = User::all()->except(1);

        return view('user.index', compact('roles', 'users'));
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
