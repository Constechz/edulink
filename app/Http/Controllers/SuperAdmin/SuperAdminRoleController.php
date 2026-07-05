<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminRoleController extends Controller
{
    /**
     * Display a listing of global roles.
     */
    public function index()
    {
        // Fetch all roles where school_id is null (global roles)
        $roles = Role::withoutGlobalScopes()
            ->whereNull('school_id')
            ->withCount('permissions')
            ->orderBy('id', 'asc')
            ->get();

        return view('super-admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new global role.
     */
    public function create()
    {
        // Fetch all permissions grouped by module
        $permissions = Permission::all()->groupBy('module');

        return view('super-admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created global role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,NULL,id,school_id,NULL',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'school_id' => null, // Global Role
                'name' => $request->name,
                'slug' => strtolower($request->slug),
                'description' => $request->description,
                'is_system' => false,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }
        });

        return redirect()->route('super-admin.roles.index')->with('success', 'Global role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(string $id)
    {
        $role = Role::withoutGlobalScopes()->whereNull('school_id')->findOrFail($id);
        $permissions = Permission::all()->groupBy('module');
        $rolePermissions = $role->permissions()->pluck('permissions.id')->toArray();

        return view('super-admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::withoutGlobalScopes()->whereNull('school_id')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id . ',id,school_id,NULL',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'slug' => strtolower($request->slug),
                'description' => $request->description,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            } else {
                $role->permissions()->detach();
            }
        });

        return redirect()->route('super-admin.roles.index')->with('success', 'Global role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::withoutGlobalScopes()->whereNull('school_id')->findOrFail($id);

        if ($role->is_system) {
            return redirect()->route('super-admin.roles.index')->withErrors(['error' => 'System roles cannot be deleted.']);
        }

        $role->delete();

        return redirect()->route('super-admin.roles.index')->with('success', 'Global role deleted successfully.');
    }
}
