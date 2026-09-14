<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('name')
            ->get();

        $roles = Role::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'users.index',
            compact(
                'users',
                'roles'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        User::create([
            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'role_id' =>
                $validated['role_id'],

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'is_active' =>
                true,
        ]);

        return back()->with(
            'success',
            'User account created successfully.'
        );
    }

    public function update(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        if (
            $user->id === auth()->id()
            && !$validated['is_active']
        ) {
            return back()->withErrors([
                'user' =>
                    'You cannot deactivate your own account.',
            ]);
        }

        $user->update($validated);

        return back()->with(
            'success',
            'User account updated successfully.'
        );
    }

    public function resetPassword(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user->update([
            'password' =>
                Hash::make(
                    $validated['password']
                ),
        ]);

        return back()->with(
            'success',
            'User password updated successfully.'
        );
    }
}
