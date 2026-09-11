@extends('layouts.app')

@section('title', 'Edit Employee | Industrial HRMS')

@section('page-heading', 'Edit Employee')

@section(
    'page-description',
    'Update employee information'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>
            {{ $employee->full_name }}
        </h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route('employees.update', $employee) }}"
        >

            @csrf
            @method('PUT')

            @include('employees._form')

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save Changes
                </button>

                <a
                    href="{{ route('employees.show', $employee) }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection
