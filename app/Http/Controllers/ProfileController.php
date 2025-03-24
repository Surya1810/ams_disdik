<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**Display the user's profile form.*/
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**Update the user's profile information.*/
    public function update(Request $request, $id)
    {
        $user = User::find(Auth::id());
        $request->validate([
            'name' => 'required|unique:users,name,' . $user->id,
        ]);

        $user->email = $request->email;
        $user->update();
        return redirect()->back()->with(['pesan' => 'Profile updated successfully', 'level-alert' => 'alert-success']);
    }
    /**Update the user's user password.*/
    public function password(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        $hashedPassword = Auth::user()->password;
        if (Hash::check($request->old_password, $hashedPassword)) {
            if (!Hash::check($request->new_password, $hashedPassword)) {
                $user = User::find(Auth::id());
                $user->password = Hash::make($request->new_password);
                $user->save();
                Auth::logout();
                return redirect()->route('login')->with(['pesan' => 'Password updated successfully', 'level-alert' => 'alert-success']);
            } else {
                return redirect()->back()->with(['pesan' => 'New password cannot be the same as old password', 'level-alert' => 'alert-danger']);
            }
        } else {
            return redirect()->back()->with(['pesan' => 'Current password not match', 'level-alert' => 'alert-danger']);
        }
    }

    /**Delete the user's account.*/
    public function destroy(Request $request, $id)
    {
        return redirect()->back()->with([
            'pesan' => 'Please contact admin',
            'level-alert' => 'alert-danger'
        ]);
    }
}
