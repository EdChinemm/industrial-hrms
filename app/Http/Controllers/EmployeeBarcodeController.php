<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeBarcode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeBarcodeController extends Controller
{
    public function store(Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($employee) {

            $employee->barcodes()
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'revoked_at' => now(),
                    'revoke_reason' => 'Replaced by a new barcode.',
                ]);

            do {
                $barcode =
                    'HRMS-' .
                    preg_replace(
                        '/[^A-Za-z0-9]/',
                        '',
                        $employee->employee_number
                    ) .
                    '-' .
                    strtoupper(Str::random(6));

            } while (
                EmployeeBarcode::where(
                    'barcode',
                    $barcode
                )->exists()
            );

            $employee->barcodes()->create([
                'barcode' => $barcode,
                'is_active' => true,
                'issued_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Employee barcode generated successfully.'
        );
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $barcode = $employee->barcodes()
            ->where('is_active', true)
            ->first();

        if (!$barcode) {
            return back()->withErrors([
                'barcode' => 'No active barcode was found.',
            ]);
        }

        $barcode->update([
            'is_active' => false,
            'revoked_at' => now(),
            'revoke_reason' => 'Barcode revoked by authorized user.',
        ]);

        return back()->with(
            'success',
            'Employee barcode revoked successfully.'
        );
    }
}
