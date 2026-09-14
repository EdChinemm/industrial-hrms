<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AttendanceDeviceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceScanController;
use App\Http\Controllers\AttendanceSyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeBarcodeController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EarningsEstimatorController;
use App\Http\Controllers\EmployeeBarcodePrintController;
use App\Http\Controllers\EdgeProvisionController;
use App\Http\Controllers\EdgeTerminalController;


/*
|--------------------------------------------------------------------------
| Standalone Edge Terminal
|--------------------------------------------------------------------------
|
| This route intentionally does not require central HRMS authentication.
| All employee, device and attendance operations use local SQLite data.
|
*/

Route::get(
    '/edge-terminal',
    [EdgeTerminalController::class, 'create']
)->name('edge-terminal');

Route::post(
    '/edge-terminal',
    [EdgeTerminalController::class, 'store']
)->name('edge-terminal.store');


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [LoginController::class, 'create']
    )->name('login');

    Route::post(
        '/login',
        [LoginController::class, 'store']
    )->name('login.store');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::post(
        '/logout',
        [LoginController::class, 'destroy']
    )->name('logout');
});


/*
|--------------------------------------------------------------------------
| HR / Administrator Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:System Administrator,HR Officer',
])->group(function () {

/*
|--------------------------------------------------------------------------
| Edge Cache Provisioning
|--------------------------------------------------------------------------
*/

Route::post(
    '/edge-devices/provision',
    [EdgeProvisionController::class, 'store']
)->name('edge-devices.provision');

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'employees',
        EmployeeController::class
    )->except([
        'destroy'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Employee Barcodes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/employees/{employee}/barcode',
        [EmployeeBarcodeController::class, 'store']
    )->name('employees.barcode.store');

    Route::delete(
        '/employees/{employee}/barcode',
        [EmployeeBarcodeController::class, 'destroy']
    )->name('employees.barcode.destroy');


    /*
    |--------------------------------------------------------------------------
    | Edge Attendance Capture
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/attendance/scan',
        [AttendanceScanController::class, 'create']
    )->name('attendance.scan');

    Route::post(
        '/attendance/scan',
        [AttendanceScanController::class, 'store']
    )->name('attendance.scan.store');


    /*
    |--------------------------------------------------------------------------
    | Edge Devices
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/edge-devices',
        [AttendanceDeviceController::class, 'index']
    )->name('edge-devices.index');

    Route::post(
        '/edge-devices/{attendanceDevice}/sync',
        [AttendanceSyncController::class, 'store']
    )->name('edge-devices.sync');


    /*
    |--------------------------------------------------------------------------
    | Leave Management
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/leave',
        [LeaveRequestController::class, 'index']
    )->name('leave.index');

    Route::post(
        '/leave',
        [LeaveRequestController::class, 'store']
    )->name('leave.store');

    Route::put(
        '/leave/{leaveRequest}/review',
        [LeaveRequestController::class, 'review']
    )->name('leave.review');
});


/*
|--------------------------------------------------------------------------
| Reporting
|--------------------------------------------------------------------------
|
| Supervisors may view reports but cannot modify HR records.
|
*/

Route::middleware([
    'auth',
    'role:System Administrator,HR Officer,Supervisor',
])->group(function () {

    Route::get(
        '/reports/attendance',
        [AttendanceReportController::class, 'index']
    )->name('reports.attendance');

    Route::get(
        '/reports/attendance/export',
        [AttendanceReportController::class, 'export']
    )->name('reports.attendance.export');

    /*
|--------------------------------------------------------------------------
| Printable Employee Barcode
|--------------------------------------------------------------------------
*/

Route::get(
    '/employees/{employee}/barcode/print',
    [EmployeeBarcodePrintController::class, 'show']
)->name('employees.barcode.print');


/*
|--------------------------------------------------------------------------
| Attendance-Based Earnings Estimator
|--------------------------------------------------------------------------
*/

Route::get(
    '/earnings',
    [EarningsEstimatorController::class, 'index']
)->name('earnings.index');

Route::post(
    '/earnings/profiles/{employee}',
    [EarningsEstimatorController::class, 'storeProfile']
)->name('earnings.profile.store');

Route::post(
    '/earnings/periods',
    [EarningsEstimatorController::class, 'storePeriod']
)->name('earnings.period.store');

Route::post(
    '/earnings/periods/{payrollPeriod}/calculate',
    [EarningsEstimatorController::class, 'calculate']
)->name('earnings.calculate');


});


/*
|--------------------------------------------------------------------------
| Default Route
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');
