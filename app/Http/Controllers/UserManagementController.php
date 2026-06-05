<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display users available for administration.
     */
    public function index(): View
    {
        return view('dashboard.users.index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    /**
     * Show the user edit form.
     */
    public function edit(User $user): View
    {
        return view('dashboard.users.edit', [
            'roles' => Role::query()->orderBy('name')->get(),
            'user' => $user->load('roles'),
        ]);
    }

    /**
     * Update a user's basic account details.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($validated);

        return redirect()
            ->route('dashboard.users.edit', $user)
            ->with('status', 'User details updated.');
    }

    /**
     * Update the roles assigned to a user.
     */
    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
        ]);

        $roles = $validated['roles'] ?? [];

        if (! $request->user()->hasRole('super_admin') && in_array('super_admin', $roles, true)) {
            abort(403);
        }

        $user->syncRoles($roles);

        return redirect()
            ->route('dashboard.users.edit', $user)
            ->with('status', 'User rights updated.');
    }
}
