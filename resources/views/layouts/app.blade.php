<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Industrial HRMS')
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/hrms.css') }}"
    >
</head>

<body>

<div class="app-shell">

    <aside class="sidebar">

        <div class="brand">
            <div class="brand-mark">HR</div>

            <div>
                <strong>Industrial HRMS</strong>
                <small>Workforce Management</small>
            </div>
        </div>

        <nav class="nav">

            <a
                href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                Dashboard
            </a>

            @if (
                in_array(
                    auth()->user()->role?->name,
                    ['System Administrator', 'HR Officer'],
                    true
                )
            )

                <a
                    href="{{ route('employees.index') }}"
                    class="{{ request()->routeIs('employees.*') ? 'active' : '' }}"
                >
                    Employees
                </a>

            @endif

            @if (
    in_array(
        auth()->user()->role?->name,
        ['System Administrator', 'HR Officer'],
        true
    )
)

    <a
        href="{{ route('attendance.scan') }}"
        class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}"
    >
        Attendance
    </a>

@else

    <span class="nav-disabled">
        Attendance
    </span>

@endif

            @if (
    in_array(
        auth()->user()->role?->name,
        ['System Administrator', 'HR Officer'],
        true
    )
)

    <a
        href="{{ route('edge-devices.index') }}"
        class="{{ request()->routeIs('edge-devices.*') ? 'active' : '' }}"
    >
        Edge Devices
    </a>

@else

    <span class="nav-disabled">
        Edge Devices
    </span>

@endif

            @if (
    in_array(
        auth()->user()->role?->name,
        ['System Administrator', 'HR Officer'],
        true
    )
)

    <a
        href="{{ route('leave.index') }}"
        class="{{ request()->routeIs('leave.*') ? 'active' : '' }}"
    >
        Leave Management
    </a>

@else

    <span class="nav-disabled">
        Leave Management
    </span>

@endif

            @if (
    in_array(
        auth()->user()->role?->name,
        [
            'System Administrator',
            'HR Officer',
            'Supervisor'
        ],
        true
    )
)

    <a
        href="{{ route('reports.attendance') }}"
        class="{{ request()->routeIs('reports.*') ? 'active' : '' }}"
    >
        Reports
    </a>

@else

    <span class="nav-disabled">
        Reports
    </span>

@endif

            <span class="nav-disabled">
                Earnings Estimator
            </span>

        </nav>

        <div class="sidebar-footer">

            <div class="sidebar-user">
                <strong>
                    {{ auth()->user()->name }}
                </strong>

                <span>
                    {{ auth()->user()->role?->name ?? 'No Role' }}
                </span>
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    Sign Out
                </button>
            </form>

        </div>

    </aside>

    <main class="main">

        <header class="topbar">

            <div>
                <h1>
                    @yield('page-heading', 'Dashboard')
                </h1>

                <p>
                    @yield(
                        'page-description',
                        'Industrial workforce management system'
                    )
                </p>
            </div>

            <div class="topbar-user">

                <span>
                    {{ now()->format('d M Y') }}
                </span>

            </div>

        </header>

        <section class="content">

            @if (session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif

            @if ($errors->any())

                <div class="alert alert-error">
                    <strong>
                        Please correct the following:
                    </strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            @endif

            @yield('content')

        </section>

    </main>

</div>

@stack('scripts')

</body>
</html>
