<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id') // ✅ Join dengan roles
            ->select(
                'users.*',
                'roles.name as role_name' // Ambil nama role
            )
            ->orderBy('users.created_at', 'desc');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('users.name', 'LIKE', "%{$request->search}%")
                ->orWhere('users.email', 'LIKE', "%{$request->search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);

        $totalUsers = DB::table('users')->count();
        $activeUsers = $totalUsers;
        $adminUsers = DB::table('users')->where('email', 'admin@example.com')->count();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminUsers'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,editor,peneliti,viewer',
        ]);

        DB::table('users')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Redirect::route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->first();

        if (!$user) {
            return Redirect::route('users.index')->with('error', 'User not found');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    // Di UserController.php
    public function edit($id)
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return Redirect::route('users.index')->with('error', 'User not found');
        }

        // Tidak perlu mengirim data role terpisah karena sudah di-handle di view
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
   public function update(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return Redirect::route('users.index')->with('error', 'User not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);
        return Redirect::route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        // Cegah penghapusan user admin
        $user = DB::table('users')->where('id', $id)->first();
        if ($user && $user->email === 'admin@example.com') {
            return Redirect::route('users.index')->with('error', 'Cannot delete admin user');
        }

        DB::table('users')->where('id', $id)->delete();
        return Redirect::route('users.index')->with('success', 'User deleted successfully!');
    }
}
