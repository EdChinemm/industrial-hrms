@extends('layouts.app')

@section('title', 'Register Employee | Industrial HRMS')

@section('page-heading', 'Register Employee')

@section(
    'page-description',
    'Create a new employee workforce record'
)

@section('content')

<div class="section" style="margin-top: 0;">

    <div class="section-header">
        <h2>Employee Information</h2>
    </div>

    <div class="section-body">

        <form
            method="POST"
            action="{{ route('employees.store') }}"
        >

            @csrf

            @include('employees._form')

            <div class="form-actions">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Register Employee
                </button>

                <a
                    href="{{ route('employees.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection
