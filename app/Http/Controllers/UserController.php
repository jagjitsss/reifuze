<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Spatie\Permission\Models\Role; // Import the Role model from Spatie
use RealRashid\SweetAlert\Facades\Alert;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;

class UserController extends Controller
{
    public function index()  {

        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }

        $users = User::all();

        return view('back.pages.userlist.index',compact('users'));

    }

    public function create()  {
        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }
        $roles = Role::get()->pluck('name', 'name');

        return view('back.pages.userlist.create',compact('roles'));

    }

    public function store(Request $request)  {

        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }
            // Validate the form data
            $validatedData = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'roles' => 'required',
                'user_status' => 'required',
                // 'user_profile' => 'nullable',
                'password' => 'required|min:6|confirmed',
                'roles' => 'array',
            ]);



            // Handle file upload
            // if ($request->hasFile('user_profile')) {
            //     $profileImage = $request->file('user_profile');
            //     $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
            //     $profileImage->move(public_path('images'), $imageName);
            // } else {
            //     $imageName = null; // If no image is uploaded
            // }

            // Create a new user instance
            $user = new User([
                'name' => $validatedData['username'],
                'email' => $validatedData['email'],
                'status' => $validatedData['user_status'],
                'password' => Hash::make($validatedData['password']),
                'time_zone' => 'Asia', // Set the time zone to "Asia"
            ]);

            // Save the user data
            $user->save();

            // Assign roles using Spatie's role package
            if ($request->input('roles')) {
                $roles = $request->input('roles') ? $request->input('roles') : [];
                $user->assignRole($roles);
            }
            session()->flash('success', 'User has been created !!');
            return redirect()->route('admin.user-list.index');


    }

    public function edit( $id)
    {
        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }
        $user = User::find($id);
        $roles = Role::get()->pluck('name', 'name');

        return view('back.pages.userlist.edit', compact('user', 'roles'));

    }

    public function update(Request $request, $id)
    {
        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }

        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'user_status' => 'required|in:0,1',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'array',
        ]);

        $user = User::findOrFail($id);

        $user->name = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->status = $validatedData['user_status'];

        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Save the user data
        $user->save();

        // Assign roles using Spatie's role package
        if ($request->input('roles')) {
            $roles = $request->input('roles') ? $request->input('roles') : [];
            $user->syncRoles($roles);
        }

        session()->flash('success', 'User has been updated !!');
        return redirect()->route('admin.user-list.index');
    }

    public function destroy( $id)
    {
        // if (! Gate::allows('users_manage')) {
        //     return abort(401);
        // }
        $user = User::find($id);
        $user->delete();
        session()->flash('success', 'User has been deleted !!');
        return redirect()->route('admin.user-list.index');
    }
}
