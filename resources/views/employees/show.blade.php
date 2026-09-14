@extends('layouts.app')

@section('title', 'Employee Profile | Industrial HRMS')

@section('page-heading', 'Employee Profile')

@section(
    'page-description',
    'Employee workforce and attendance information'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">

        <div>
            <h2>
                {{ $employee->full_name }}
            </h2>
        </div>

        <a
            href="{{ route('employees.edit', $employee) }}"
            class="btn btn-primary"
        >
            Edit Employee
        </a>

    </div>

    <div class="section-body">

        <div class="profile-grid">

            <div class="profile-item">
                <span>Employee Number</span>
                <strong>
                    {{ $employee->employee_number }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Department</span>
                <strong>
                    {{ $employee->department->name }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Position</span>
                <strong>
                    {{ $employee->position->title }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Status</span>
                <strong>
                    {{ ucfirst($employee->employment_status) }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Phone</span>
                <strong>
                    {{ $employee->phone ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Email</span>
                <strong>
                    {{ $employee->email ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>National ID</span>
                <strong>
                    {{ $employee->national_id ?: '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Employment Date</span>
                <strong>
                    {{ $employee->employment_date?->format('d M Y') ?? '—' }}
                </strong>
            </div>

            <div class="profile-item">
                <span>Attendance Records</span>
                <strong>
                    {{ $employee->attendanceRecords->count() }}
                </strong>
            </div>

        </div>

    </div>

</div>

<div class="section">

    <div class="section-header">
        <h2>Attendance Summary</h2>
    </div>

    <div class="section-body">

        <p>
            Recorded attendance entries:
            <strong>
                {{ $employee->attendanceRecords->count() }}
            </strong>
        </p>

        <p>
            This employee will later receive a barcode for
            Edge-based attendance capture.
        </p>

    </div>

</div>

<div class="section">

    <div class="section-header">

        <h2>Attendance Barcode</h2>

    </div>

    <div class="section-body">

        @if ($employee->activeBarcode)

            <p>
                Active Barcode:
            </p>

            <div
                style="
                    font-size: 22px;
                    font-weight: bold;
                    letter-spacing: 2px;
                    margin: 15px 0;
                "
            >
                {{ $employee->activeBarcode->barcode }}
            </div>

            <p>
                Issued:
                {{ $employee->activeBarcode->issued_at?->format('d M Y H:i') }}
            </p>

            <div class="form-actions">

                <form
                    method="POST"
                    action="{{ route('employees.barcode.store', $employee) }}"
                >
                    @csrf


                    <a
    href="{{ route(
        'employees.barcode.print',
        $employee
    ) }}"
    class="btn btn-primary"
    target="_blank"
>
    Print Barcode
</a>
                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Replace Barcode
                    </button>
                </form>

                <form
                    method="POST"
                    action="{{ route('employees.barcode.destroy', $employee) }}"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Revoke Barcode
                    </button>
                </form>

            </div>

        @else

            <p>
                No active attendance barcode has been assigned.
            </p>

            <form
                method="POST"
                action="{{ route('employees.barcode.store', $employee) }}"
            >
                @csrf

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Generate Attendance Barcode
                </button>

            </form>

        @endif

    </div>

</div>

<div class="section">

    <div class="section-header">
        <h2>Employee Documents</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route(
                'employees.documents.store',
                $employee
            ) }}"
            enctype="multipart/form-data"
        >

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Document Type *
                    </label>

                    <select
                        name="document_type"
                        class="form-control"
                        required
                    >

                        <option value="">
                            Select Type
                        </option>

                        <option value="National ID">
                            National ID
                        </option>

                        <option value="Employment Contract">
                            Employment Contract
                        </option>

                        <option value="Certificate">
                            Certificate
                        </option>

                        <option value="Medical">
                            Medical
                        </option>

                        <option value="Training">
                            Training
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Document Name *
                    </label>

                    <input
                        type="text"
                        name="document_name"
                        class="form-control"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>
                        Issue Date
                    </label>

                    <input
                        type="date"
                        name="issue_date"
                        class="form-control"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Expiry Date
                    </label>

                    <input
                        type="date"
                        name="expiry_date"
                        class="form-control"
                    >

                </div>

                <div class="form-group full">

                    <label>
                        File *
                    </label>

                    <input
                        type="file"
                        name="document"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                    >

                    <small>
                        PDF, JPG or PNG. Maximum 5 MB.
                    </small>

                </div>

                <div class="form-group full">

                    <label>
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                    ></textarea>

                </div>

            </div>

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Upload Document
                </button>

            </div>

        </form>

    </div>

</div>


<div class="section">

    <div class="section-header">
        <h2>Stored Documents</h2>
    </div>

    <div class="table-wrap">

        @if ($employee->documents->isEmpty())

            <div class="empty-state">
                No documents have been uploaded
                for this employee.
            </div>

        @else

            <table>

                <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Issued</th>
                    <th>Expires</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>

                @foreach (
                    $employee->documents
                    as $document
                )

                    <tr>

                        <td>
                            {{ $document->document_name }}
                        </td>

                        <td>
                            {{ $document->document_type }}
                        </td>

                        <td>
                            {{ $document->issue_date
                                ?->format('d M Y')
                                ?? '—' }}
                        </td>

                        <td>
                            {{ $document->expiry_date
                                ?->format('d M Y')
                                ?? '—' }}
                        </td>

                        <td>

                            <div class="actions">

                                <a
                                    href="{{ route(
                                        'employees.documents.download',
                                        [
                                            $employee,
                                            $document
                                        ]
                                    ) }}"
                                    class="btn btn-secondary"
                                >
                                    Download
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'employees.documents.destroy',
                                        [
                                            $employee,
                                            $document
                                        ]
                                    ) }}"
                                    onsubmit="
                                        return confirm(
                                            'Remove this document?'
                                        );
                                    "
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-secondary"
                                    >
                                        Remove
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @endif

    </div>

</div>

@endsection
