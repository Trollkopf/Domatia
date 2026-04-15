<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('userGroup')->orderBy('name')->paginate(15);
        $allUsers = User::with('userGroup')->get();
        $groups = UserGroup::withCount('users')
            ->orderByDesc('can_manage_users')
            ->orderByDesc('can_access_backoffice')
            ->orderBy('name')
            ->get();
        $stats = [
            'users_total' => $allUsers->count(),
            'backoffice_users' => $allUsers->filter(fn (User $user) => $user->canAccessBackoffice())->count(),
            'management_users' => $allUsers->filter(fn (User $user) => $user->canManageUsers())->count(),
        ];

        return view('admin.users.index', compact('users', 'groups', 'stats'));
    }

    public function create()
    {
        $user = new User();
        $groups = UserGroup::query()
            ->orderByDesc('can_manage_users')
            ->orderByDesc('can_access_backoffice')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('user', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_group_id' => ['required', Rule::exists('user_groups', 'id')],
        ]);

        $group = UserGroup::findOrFail($validated['user_group_id']);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $group->slug,
            'user_group_id' => $group->id,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $groups = UserGroup::query()
            ->orderByDesc('can_manage_users')
            ->orderByDesc('can_access_backoffice')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'groups'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'user_group_id' => ['required', Rule::exists('user_groups', 'id')],
        ]);

        $group = UserGroup::findOrFail($validated['user_group_id']);

        if ((int) $request->user()->id === (int) $user->id && ! $group->can_manage_users) {
            return back()
                ->withInput()
                ->withErrors([
                    'user_group_id' => 'No puedes quitarte a ti mismo los permisos de gestion de usuarios desde esta pantalla.',
                ]);
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $group->slug,
            'user_group_id' => $group->id,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $user)
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario desde el backoffice.');
        }

        if ($user->canManageUsers() && ! User::query()
            ->whereKeyNot($user->id)
            ->with('userGroup')
            ->get()
            ->contains(fn (User $candidate) => $candidate->canManageUsers())) {
            return back()->with('error', 'No puedes eliminar el ultimo usuario con permisos de gestion.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'group_name' => 'required|string|max:255|unique:user_groups,name',
            'group_description' => 'nullable|string|max:2000',
            'group_can_access_backoffice' => 'nullable|boolean',
            'group_can_manage_users' => 'nullable|boolean',
            'group_can_manage_settings' => 'nullable|boolean',
            'group_can_manage_properties' => 'nullable|boolean',
            'group_can_publish_properties' => 'nullable|boolean',
            'group_can_manage_contacts' => 'nullable|boolean',
            'group_can_manage_zonas' => 'nullable|boolean',
            'group_can_view_reports' => 'nullable|boolean',
            'group_can_export_reports' => 'nullable|boolean',
        ]);

        $permissions = $this->extractGroupPermissions($request, 'group_');

        UserGroup::create([
            'name' => $validated['group_name'],
            'slug' => UserGroup::generateUniqueSlug($validated['group_name']),
            'description' => $validated['group_description'] ?? null,
            ...$permissions,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Grupo creado correctamente.');
    }

    public function updateGroup(Request $request, UserGroup $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('user_groups', 'name')->ignore($group->id)],
            'description' => 'nullable|string|max:2000',
            'can_access_backoffice' => 'nullable|boolean',
            'can_manage_users' => 'nullable|boolean',
            'can_manage_settings' => 'nullable|boolean',
            'can_manage_properties' => 'nullable|boolean',
            'can_publish_properties' => 'nullable|boolean',
            'can_manage_contacts' => 'nullable|boolean',
            'can_manage_zonas' => 'nullable|boolean',
            'can_view_reports' => 'nullable|boolean',
            'can_export_reports' => 'nullable|boolean',
        ]);

        $permissions = $this->extractGroupPermissions($request);
        $canManageUsers = $permissions['can_manage_users'];
        $canAccessBackoffice = $permissions['can_access_backoffice'];

        if ($group->users()->whereKey($request->user()->id)->exists() && ! $canManageUsers) {
            return back()->with('error', 'No puedes quitarle a tu propio grupo los permisos de gestion de usuarios.');
        }

        if ($group->users()->whereKey($request->user()->id)->exists() && ! $canAccessBackoffice) {
            return back()->with('error', 'No puedes quitarle a tu propio grupo el acceso al backoffice.');
        }

        if ($group->can_manage_users
            && ! $canManageUsers
            && ! UserGroup::query()->where('can_manage_users', true)->whereKeyNot($group->id)->exists()) {
            return back()->with('error', 'Debe existir al menos un grupo con permisos para gestionar usuarios.');
        }

        $oldSlug = $group->slug;

        $group->update([
            'name' => $validated['name'],
            'slug' => UserGroup::generateUniqueSlug($validated['name'], $group->id),
            'description' => $validated['description'] ?? null,
            ...$permissions,
        ]);

        if ($oldSlug !== $group->slug || $group->users()->exists()) {
            $group->users()->update(['role' => $group->slug]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Grupo actualizado correctamente.');
    }

    public function destroyGroup(UserGroup $group)
    {
        if ($group->users()->exists()) {
            return back()->with('error', 'No puedes eliminar un grupo que todavia tiene usuarios asignados.');
        }

        if ($group->can_manage_users
            && ! UserGroup::query()->where('can_manage_users', true)->whereKeyNot($group->id)->exists()) {
            return back()->with('error', 'Debe existir al menos un grupo con permisos para gestionar usuarios.');
        }

        $group->delete();

        return redirect()->route('admin.users.index')->with('success', 'Grupo eliminado correctamente.');
    }

    private function extractGroupPermissions(Request $request, string $prefix = ''): array
    {
        $permissions = [
            'can_access_backoffice' => $request->boolean($prefix . 'can_access_backoffice'),
            'can_manage_users' => $request->boolean($prefix . 'can_manage_users'),
            'can_manage_settings' => $request->boolean($prefix . 'can_manage_settings'),
            'can_manage_properties' => $request->boolean($prefix . 'can_manage_properties'),
            'can_publish_properties' => $request->boolean($prefix . 'can_publish_properties'),
            'can_manage_contacts' => $request->boolean($prefix . 'can_manage_contacts'),
            'can_manage_zonas' => $request->boolean($prefix . 'can_manage_zonas'),
            'can_view_reports' => $request->boolean($prefix . 'can_view_reports'),
            'can_export_reports' => $request->boolean($prefix . 'can_export_reports'),
        ];

        $permissions['can_manage_properties'] = $permissions['can_manage_properties'] || $permissions['can_publish_properties'];
        $permissions['can_view_reports'] = $permissions['can_view_reports'] || $permissions['can_export_reports'];

        $permissions['can_access_backoffice'] = $permissions['can_access_backoffice']
            || $permissions['can_manage_users']
            || $permissions['can_manage_settings']
            || $permissions['can_manage_properties']
            || $permissions['can_publish_properties']
            || $permissions['can_manage_contacts']
            || $permissions['can_manage_zonas']
            || $permissions['can_view_reports'];

        return $permissions;
    }
}
