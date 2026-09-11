@php
    $editing = isset($employee);
@endphp

<div class="form-grid">

    <div class="form-group">

        <label for="employee_number">
            Employee Number *
        </label>

        <input
            class="form-control"
            type="text"
            id="employee_number"
            name="employee_number"
            value="{{ old('employee_number', $editing ? $employee->employee_number : '') }}"
            required
        >

    </div>

    <div class="form-group">

        <label for="department_id">
            Department *
        </label>

        <select
            class="form-control"
            id="department_id"
            name="department_id"
            required
        >

            <option value="">
                Select Department
            </option>

            @foreach ($departments as $department)

                <option
                    value="{{ $department->id }}"
                    @selected(
                        old(
                            'department_id',
                            $editing ? $employee->department_id : ''
                        ) == $department->id
                    )
                >
                    {{ $department->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label for="position_id">
            Position *
        </label>

        <select
            class="form-control"
            id="position_id"
            name="position_id"
            required
        >

            <option value="">
                Select Position
            </option>

            @foreach ($positions as $position)

                <option
                    value="{{ $position->id }}"
                    data-department="{{ $position->department_id }}"
                    @selected(
                        old(
                            'position_id',
                            $editing ? $employee->position_id : ''
                        ) == $position->id
                    )
                >
                    {{ $position->title }}
                    — {{ $position->department->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label for="employment_status">
            Employment Status *
        </label>

        <select
            class="form-control"
            id="employment_status"
            name="employment_status"
            required
        >

            @foreach (
                [
                    'active',
                    'inactive',
                    'suspended',
                    'terminated'
                ] as $status
            )

                <option
                    value="{{ $status }}"
                    @selected(
                        old(
                            'employment_status',
                            $editing
                                ? $employee->employment_status
                                : 'active'
                        ) === $status
                    )
                >
                    {{ ucfirst($status) }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label for="first_name">
            First Name *
        </label>

        <input
            class="form-control"
            type="text"
            id="first_name"
            name="first_name"
            value="{{ old('first_name', $editing ? $employee->first_name : '') }}"
            required
        >

    </div>

    <div class="form-group">

        <label for="middle_name">
            Middle Name
        </label>

        <input
            class="form-control"
            type="text"
            id="middle_name"
            name="middle_name"
            value="{{ old('middle_name', $editing ? $employee->middle_name : '') }}"
        >

    </div>

    <div class="form-group">

        <label for="last_name">
            Last Name *
        </label>

        <input
            class="form-control"
            type="text"
            id="last_name"
            name="last_name"
            value="{{ old('last_name', $editing ? $employee->last_name : '') }}"
            required
        >

    </div>

    <div class="form-group">

        <label for="gender">
            Gender
        </label>

        <select
            class="form-control"
            id="gender"
            name="gender"
        >

            <option value="">
                Not specified
            </option>

            <option
                value="Male"
                @selected(
                    old(
                        'gender',
                        $editing ? $employee->gender : ''
                    ) === 'Male'
                )
            >
                Male
            </option>

            <option
                value="Female"
                @selected(
                    old(
                        'gender',
                        $editing ? $employee->gender : ''
                    ) === 'Female'
                )
            >
                Female
            </option>

        </select>

    </div>

    <div class="form-group">

        <label for="date_of_birth">
            Date of Birth
        </label>

        <input
            class="form-control"
            type="date"
            id="date_of_birth"
            name="date_of_birth"
            value="{{ old(
                'date_of_birth',
                $editing && $employee->date_of_birth
                    ? $employee->date_of_birth->format('Y-m-d')
                    : ''
            ) }}"
        >

    </div>

    <div class="form-group">

        <label for="national_id">
            National ID
        </label>

        <input
            class="form-control"
            type="text"
            id="national_id"
            name="national_id"
            value="{{ old('national_id', $editing ? $employee->national_id : '') }}"
        >

    </div>

    <div class="form-group">

        <label for="phone">
            Phone
        </label>

        <input
            class="form-control"
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $editing ? $employee->phone : '') }}"
        >

    </div>

    <div class="form-group">

        <label for="email">
            Email
        </label>

        <input
            class="form-control"
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $editing ? $employee->email : '') }}"
        >

    </div>

    <div class="form-group">

        <label for="employment_date">
            Employment Date
        </label>

        <input
            class="form-control"
            type="date"
            id="employment_date"
            name="employment_date"
            value="{{ old(
                'employment_date',
                $editing && $employee->employment_date
                    ? $employee->employment_date->format('Y-m-d')
                    : ''
            ) }}"
        >

    </div>

    <div class="form-group">

        <label for="termination_date">
            Termination Date
        </label>

        <input
            class="form-control"
            type="date"
            id="termination_date"
            name="termination_date"
            value="{{ old(
                'termination_date',
                $editing && $employee->termination_date
                    ? $employee->termination_date->format('Y-m-d')
                    : ''
            ) }}"
        >

    </div>

    <div class="form-group full">

        <label for="address">
            Address
        </label>

        <textarea
            class="form-control"
            id="address"
            name="address"
        >{{ old('address', $editing ? $employee->address : '') }}</textarea>

    </div>

    <div class="form-group full">

        <label for="notes">
            Notes
        </label>

        <textarea
            class="form-control"
            id="notes"
            name="notes"
        >{{ old('notes', $editing ? $employee->notes : '') }}</textarea>

    </div>

</div>

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const department = document.getElementById('department_id');
    const position = document.getElementById('position_id');

    function filterPositions() {

        const departmentId = department.value;

        Array.from(position.options).forEach(function (option) {

            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                departmentId &&
                option.dataset.department !== departmentId;
        });

        const selected = position.options[position.selectedIndex];

        if (
            selected &&
            selected.value &&
            selected.hidden
        ) {
            position.value = '';
        }
    }

    department.addEventListener(
        'change',
        filterPositions
    );

    filterPositions();
});
</script>

@endpush
