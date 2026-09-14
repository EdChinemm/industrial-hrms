@extends('layouts.app')

@section(
    'title',
    'User Management | Industrial HRMS'
)

@section(
    'page-heading',
    'User Management'
)

@section(
    'page-description',
    'System accounts, roles and access control'
)

@section('content')


<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Create User Account</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route('users.store') }}"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Name *</label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Email *</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Role *</label>

                    <select
                        name="role_id"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Select Role
                        </option>

                        @foreach ($roles as $role)

                            <option
                                value="{{ $role->id }}"
                            >
                                {{ $role->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Password *</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Confirm Password *
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required
                    >

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Create User
                </button>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>System Users</h2>
    </div>

    <div class="table-wrap">

        <table>

            <thead>

            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Account</th>
                <th>Password</th>
            </tr>

            </thead>

            <tbody>

            @foreach ($users as $user)

                <tr>

                    <td>
                        {{ $user->name }}
                    </td>

                    <td>
                        {{ $user->email }}
                    </td>

                    <td colspan="3">

                        <form
                            method="POST"
                            action="{{ route(
                                'users.update',
                                $user
                            ) }}"
                        >

                            @csrf
                            @method('PUT')

                            <div
                                class="actions"
                                style="
                                    align-items: center;
                                    flex-wrap: wrap;
                                "
                            >

                                <input
                                    type="hidden"
                                    name="name"
                                    value="{{ $user->name }}"
                                >

                                <input
                                    type="hidden"
                                    name="email"
                                    value="{{ $user->email }}"
                                >

                                <select
                                    name="role_id"
                                    class="form-control"
                                    style="width: 180px;"
                                >

                                    @foreach ($roles as $role)

                                        <option
                                            value="{{ $role->id }}"
                                            @selected(
                                                $user->role_id
                                                === $role->id
                                            )
                                        >
                                            {{ $role->name }}
                                        </option>

                                    @endforeach

                                </select>

                                <select
                                    name="is_active"
                                    class="form-control"
                                    style="width: 110px;"
                                >

                                    <option
                                        value="1"
                                        @selected(
                                            $user->is_active
                                        )
                                    >
                                        Active
                                    </option>

                                    <option
                                        value="0"
                                        @selected(
                                            !$user->is_active
                                        )
                                    >
                                        Inactive
                                    </option>

                                </select>

                                <button
                                    type="submit"
                                    class="btn btn-secondary"
                                >
                                    Update
                                </button>

                            </div>

                        </form>

                    </td>

                    <td>

                        <form
                            method="POST"
                            action="{{ route(
                                'users.password',
                                $user
                            ) }}"
                        >

                            @csrf
                            @method('PUT')

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="New password"
                                required
                                style="
                                    margin-bottom: 6px;
                                    min-width: 150px;
                                "
                            >

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Confirm"
                                required
                                style="
                                    margin-bottom: 6px;
                                    min-width: 150px;
                                "
                            >

                            <button
                                type="submit"
                                class="btn btn-secondary"
                            >
                                Reset
                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
