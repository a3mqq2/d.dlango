<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissionGroups = Permission::getGrouped();
        return view('users.create', compact('permissionGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,user'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        $user = User::create($validated);

        // Sync permissions for non-admin users
        if ($validated['role'] !== 'admin') {
            $user->syncPermissions($permissions);
        }

        return redirect()->route('users.index')
            ->with('success', __('messages.user_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('transactions');
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $permissionGroups = Permission::getGrouped();
        $userPermissions = $user->permissions()->pluck('id')->toArray();
        return view('users.edit', compact('user', 'permissionGroups', 'userPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', 'in:admin,user'],
            'is_active' => ['nullable', 'boolean'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        $user->update($validated);

        // Sync permissions for non-admin users
        if ($validated['role'] !== 'admin') {
            $user->syncPermissions($permissions);
        } else {
            // Remove permissions for admin users (they have all by default)
            $user->permissions()->detach();
        }

        return redirect()->route('users.index')
            ->with('success', __('messages.user_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', __('messages.cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('messages.user_deleted'));
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', __('messages.cannot_deactivate_self'));
        }

        $user->update(['is_active' => !$user->is_active]);

        return redirect()->back()
            ->with('success', __('messages.status_updated'));
    }
}
