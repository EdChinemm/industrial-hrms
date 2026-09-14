@extends('layouts.app')

@section(
    'title',
    'Audit Logs | Industrial HRMS'
)

@section(
    'page-heading',
    'Audit Logs'
)

@section(
    'page-description',
    'System activity and accountability records'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Audit Filters</h2>
    </div>

    <div class="section-body">

        <form
            method="GET"
            action="{{ route('audit.index') }}"
        >

            <div class="form-grid">

                <div class="form-group">

                    <label>User</label>

                    <select
                        name="user_id"
                        class="form-control"
                    >

                        <option value="">
                            All Users
                        </option>

                        @foreach ($users as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected(
                                    request('user_id')
                                    == $user->id
                                )
                            >
                                {{ $user->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>Action</label>

                    <input
                        type="text"
                        name="action"
                        class="form-control"
                        value="{{ request('action') }}"
                        placeholder="e.g. leave.review"
                    >

                </div>

                <div class="form-group">

                    <label>Date</label>

                    <input
                        type="date"
                        name="date"
                        class="form-control"
                        value="{{ request('date') }}"
                    >

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Apply Filters
                </button>

                <a
                    href="{{ route('audit.index') }}"
                    class="btn btn-secondary"
                >
                    Clear
                </a>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Activity History</h2>
    </div>

    <div class="table-wrap">

        @if ($logs->isEmpty())

            <div class="empty-state">
                No audit activity has been recorded.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Date / Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Method</th>
                    <th>Path</th>
                    <th>Result</th>
                    <th>IP</th>
                </tr>
                </thead>

                <tbody>

                @foreach ($logs as $log)

                    <tr>

                        <td>
                            {{ $log->created_at
                                ->format(
                                    'd M Y H:i:s'
                                ) }}
                        </td>

                        <td>
                            {{ $log->user?->name
                                ?? 'System' }}
                        </td>

                        <td>
                            {{ $log->action }}
                        </td>

                        <td>
                            {{ $log->method }}
                        </td>

                        <td>
                            {{ $log->path }}
                        </td>

                        <td>
                            {{ $log->status_code }}
                        </td>

                        <td>
                            {{ $log->ip_address
                                ?? '—' }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <div class="section-body">
                {{ $logs->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
