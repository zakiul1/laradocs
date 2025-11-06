<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Only super_admin should be able to hit this (middleware handles it)
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Prevent creating another super_admin unless current is super_admin
        if (!$request->user()->isSuperAdmin() && $data['role'] === User::ROLE_SUPER_ADMIN) {
            abort(403, 'Only super admin can assign super admin role.');
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (!$request->user()->isSuperAdmin() && $data['role'] === User::ROLE_SUPER_ADMIN) {
            abort(403, 'Only super admin can assign super admin role.');
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->is_active = $data['is_active'] ?? $user->is_active;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    public function destroy(User $user)
    {
        // Soft delete if enabled
        $user->delete();
        return back()->with('status', 'User deactivated.');
    }

    // Optional explicit deactivate endpoint (if you prefer not to use destroy)
    public function deactivate(User $user)
    {
        $user->is_active = false;
        $user->save();

        return back()->with('status', 'User deactivated.');
    }
}